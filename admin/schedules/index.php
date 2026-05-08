<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_header.php";

$message = "";
$messageClass = "";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageClass = isset($_GET['success']) && $_GET['success'] == '1' ? 'alert-success' : 'alert-error';
}

// Fetch all schedules with route and driver info
$schedules = [];
$sql = "
    SELECT 
        s.schedule_id,
        s.departure_time,
        s.arrival_time,
        r.route_name,
        d.driver_name,
        d.phone_number as driver_phone
    FROM schedule s
    JOIN route r ON r.route_id = s.route_id
    JOIN driver d ON d.driver_id = s.driver_id
    ORDER BY s.departure_time ASC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $schedules[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Schedules Management</h1>
    <p class="admin-page-subtitle">Manage bus schedules in the system</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Schedules</h2>
        <a href="/bus/admin/schedules/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add New Schedule
        </a>
    </div>

    <?php if (!empty($schedules)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Route</th>
                        <th>Driver</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($schedule['schedule_id']); ?></td>
                            <td><?php echo htmlspecialchars($schedule['route_name']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($schedule['driver_name']); ?>
                                <br><small style="color: #7f1d1d;"><?php echo htmlspecialchars($schedule['driver_phone']); ?></small>
                            </td>
                            <td><?php echo date('H:i', strtotime($schedule['departure_time'])); ?></td>
                            <td><?php echo date('H:i', strtotime($schedule['arrival_time'])); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="/bus/admin/schedules/edit.php?id=<?php echo $schedule['schedule_id']; ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </a>
                                    <a href="/bus/admin/schedules/delete.php?id=<?php echo $schedule['schedule_id']; ?>" class="admin-btn admin-btn-danger admin-btn-sm" onclick="return confirm('Are you sure you want to delete this schedule?');">
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
            <i class="fa-solid fa-clock"></i>
            <p>No schedules found.</p>
            <a href="/bus/admin/schedules/add.php" class="admin-btn admin-btn-primary">Add First Schedule</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

