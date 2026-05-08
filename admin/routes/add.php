<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_route'])) {
    $route_name = trim($_POST['route_name'] ?? '');

    if (empty($route_name)) {
        $message = "Route name is required";
        $messageClass = "alert-error";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO route (route_name) VALUES (?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $route_name);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/routes/index.php?message=Route added successfully&success=1");
                exit();
            } else {
                $message = "Error: " . mysqli_error($conn);
                $messageClass = "alert-error";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Error preparing statement";
            $messageClass = "alert-error";
        }
    }
}

include __DIR__ . "/../../includes/admin_header.php";
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Add New Route</h1>
    <p class="admin-page-subtitle">Create a new bus route</p>
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
                placeholder="e.g., Campus to City Center"
                required
                value="<?php echo htmlspecialchars($_POST['route_name'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <button type="submit" name="add_route" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Add Route
            </button>
            <a href="/bus/admin/routes/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

