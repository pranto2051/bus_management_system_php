<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_header.php";

$message = "";
$messageClass = "";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageClass = isset($_GET['success']) && $_GET['success'] == '1' ? 'alert-success' : 'alert-error';
}

// Fetch all buses with route, schedule, and driver info
$buses = [];
$sql = "
    SELECT 
        b.bus_id,
        b.bus_num,
        b.capacity,
        r.route_name,
        s.departure_time,
        s.arrival_time,
        d.driver_name
    FROM bus b
    JOIN route r ON r.route_id = b.route_id
    JOIN schedule s ON s.schedule_id = b.schedule_id
    JOIN driver d ON d.driver_id = s.driver_id
    ORDER BY b.bus_num ASC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $buses[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Buses Management</h1>
    <p class="admin-page-subtitle">Manage buses in the system</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Buses</h2>
        <a href="/bus/admin/buses/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add New Bus
        </a>
    </div>

    <?php if (!empty($buses)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bus Number</th>
                        <th>Capacity</th>
                        <th>Route</th>
                        <th>Schedule</th>
                        <th>Driver</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($buses as $bus): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bus['bus_id']); ?></td>
                            <td><?php echo htmlspecialchars($bus['bus_num']); ?></td>
                            <td><?php echo htmlspecialchars($bus['capacity']); ?> seats</td>
                            <td><?php echo htmlspecialchars($bus['route_name']); ?></td>
                            <td>
                                <?php echo date('H:i', strtotime($bus['departure_time'])); ?> - 
                                <?php echo date('H:i', strtotime($bus['arrival_time'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($bus['driver_name']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="/bus/admin/buses/edit.php?id=<?php echo $bus['bus_id']; ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </a>
                                    <a href="/bus/admin/buses/delete.php?id=<?php echo $bus['bus_id']; ?>" class="admin-btn admin-btn-danger admin-btn-sm" onclick="return confirm('Are you sure you want to delete this bus?');">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <i class="fa-solid fa-bus"></i>
            <p>No buses found.</p>
            <a href="/bus/admin/buses/add.php" class="admin-btn admin-btn-primary">Add First Bus</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

