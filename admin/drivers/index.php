<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_header.php";

$message = "";
$messageClass = "";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageClass = isset($_GET['success']) && $_GET['success'] == '1' ? 'alert-success' : 'alert-error';
}

// Fetch all drivers with schedule count
$drivers = [];
$sql = "
    SELECT 
        d.driver_id,
        d.driver_name,
        d.phone_number,
        COUNT(DISTINCT s.schedule_id) as schedule_count
    FROM driver d
    LEFT JOIN schedule s ON s.driver_id = d.driver_id
    GROUP BY d.driver_id, d.driver_name, d.phone_number
    ORDER BY d.driver_name ASC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $drivers[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Drivers Management</h1>
    <p class="admin-page-subtitle">Manage drivers in the system</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Drivers</h2>
        <a href="/bus/admin/drivers/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add New Driver
        </a>
    </div>

    <?php if (!empty($drivers)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Driver Name</th>
                        <th>Phone Number</th>
                        <th>Assigned Schedules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drivers as $driver): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($driver['driver_id']); ?></td>
                            <td><?php echo htmlspecialchars($driver['driver_name']); ?></td>
                            <td><?php echo htmlspecialchars($driver['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($driver['schedule_count']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="/bus/admin/drivers/edit.php?id=<?php echo $driver['driver_id']; ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </a>
                                    <a href="/bus/admin/drivers/delete.php?id=<?php echo $driver['driver_id']; ?>" class="admin-btn admin-btn-danger admin-btn-sm" onclick="return confirm('Are you sure you want to delete this driver?');">
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
            <i class="fa-solid fa-id-card"></i>
            <p>No drivers found.</p>
            <a href="/bus/admin/drivers/add.php" class="admin-btn admin-btn-primary">Add First Driver</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

