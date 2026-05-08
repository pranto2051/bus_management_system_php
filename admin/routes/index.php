<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_header.php";

$message = "";
$messageClass = "";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageClass = isset($_GET['success']) && $_GET['success'] == '1' ? 'alert-success' : 'alert-error';
}

// Fetch all routes with counts
$routes = [];
$sql = "
    SELECT 
        r.route_id,
        r.route_name,
        COUNT(DISTINCT rs.stop_id) as stop_count,
        COUNT(DISTINCT s.schedule_id) as schedule_count
    FROM route r
    LEFT JOIN route_stops rs ON rs.route_id = r.route_id
    LEFT JOIN schedule s ON s.route_id = r.route_id
    GROUP BY r.route_id, r.route_name
    ORDER BY r.route_name ASC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $routes[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Routes Management</h1>
    <p class="admin-page-subtitle">Manage bus routes in the system</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Routes</h2>
        <a href="/bus/admin/routes/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add New Route
        </a>
    </div>

    <?php if (!empty($routes)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Route Name</th>
                        <th>Stops</th>
                        <th>Schedules</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($routes as $route): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($route['route_id']); ?></td>
                            <td><?php echo htmlspecialchars($route['route_name']); ?></td>
                            <td><?php echo htmlspecialchars($route['stop_count']); ?></td>
                            <td><?php echo htmlspecialchars($route['schedule_count']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="/bus/admin/routes/edit.php?id=<?php echo $route['route_id']; ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </a>
                                    <a href="/bus/admin/routes/delete.php?id=<?php echo $route['route_id']; ?>" class="admin-btn admin-btn-danger admin-btn-sm" onclick="return confirm('Are you sure you want to delete this route?');">
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
            <i class="fa-solid fa-route"></i>
            <p>No routes found.</p>
            <a href="/bus/admin/routes/add.php" class="admin-btn admin-btn-primary">Add First Route</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

