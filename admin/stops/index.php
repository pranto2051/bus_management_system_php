<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_header.php";

$message = "";
$messageClass = "";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageClass = isset($_GET['success']) && $_GET['success'] == '1' ? 'alert-success' : 'alert-error';
}

// Fetch all stops with route count
$stops = [];
$sql = "
    SELECT 
        s.stop_id,
        s.stop_name,
        COUNT(DISTINCT rs.route_id) as route_count
    FROM stop s
    LEFT JOIN route_stops rs ON rs.stop_id = s.stop_id
    GROUP BY s.stop_id, s.stop_name
    ORDER BY s.stop_name ASC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stops[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Stops Management</h1>
    <p class="admin-page-subtitle">Manage bus stops in the system</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Stops</h2>
        <a href="/bus/admin/stops/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add New Stop
        </a>
    </div>

    <?php if (!empty($stops)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Stop Name</th>
                        <th>Used in Routes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stops as $stop): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stop['stop_id']); ?></td>
                            <td><?php echo htmlspecialchars($stop['stop_name']); ?></td>
                            <td><?php echo htmlspecialchars($stop['route_count']); ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="/bus/admin/stops/edit.php?id=<?php echo $stop['stop_id']; ?>" class="admin-btn admin-btn-secondary admin-btn-sm">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </a>
                                    <a href="/bus/admin/stops/delete.php?id=<?php echo $stop['stop_id']; ?>" class="admin-btn admin-btn-danger admin-btn-sm" onclick="return confirm('Are you sure you want to delete this stop?');">
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
            <i class="fa-solid fa-map-marker-alt"></i>
            <p>No stops found.</p>
            <a href="/bus/admin/stops/add.php" class="admin-btn admin-btn-primary">Add First Stop</a>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

