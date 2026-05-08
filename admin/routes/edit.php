<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";
$route = null;

$route_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($route_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT route_id, route_name FROM route WHERE route_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $route_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $route = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

if (!$route) {
    header("Location: /bus/admin/routes/index.php?message=Route not found&success=0");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_route'])) {
    $route_name = trim($_POST['route_name'] ?? '');

    if (empty($route_name)) {
        $message = "Route name is required";
        $messageClass = "alert-error";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE route SET route_name = ? WHERE route_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $route_name, $route_id);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/routes/index.php?message=Route updated successfully&success=1");
                exit();
            } else {
                $message = "Error: " . mysqli_error($conn);
                $messageClass = "alert-error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

include __DIR__ . "/../../includes/admin_header.php";
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Edit Route</h1>
    <p class="admin-page-subtitle">Update route information</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <div class="admin-form-group">
            <label for="route_name" class="admin-form-label">Route Name</label>
            <input
                type="text"
                id="route_name"
                name="route_name"
                class="admin-form-control"
                required
                value="<?php echo htmlspecialchars($route['route_name']); ?>"
            >
        </div>

        <div class="admin-form-group">
            <button type="submit" name="update_route" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Update Route
            </button>
            <a href="/bus/admin/routes/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

