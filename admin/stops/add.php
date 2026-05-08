<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stop'])) {
    $stop_name = trim($_POST['stop_name'] ?? '');

    if (empty($stop_name)) {
        $message = "Stop name is required";
        $messageClass = "alert-error";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO stop (stop_name) VALUES (?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $stop_name);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/stops/index.php?message=Stop added successfully&success=1");
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
    <h1 class="admin-page-title">Add New Stop</h1>
    <p class="admin-page-subtitle">Create a new bus stop</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <div class="admin-form-group">
            <label for="stop_name" class="admin-form-label">Stop Name</label>
            <input
                type="text"
                id="stop_name"
                name="stop_name"
                class="admin-form-control"
                placeholder="e.g., Campus Main Gate"
                required
                value="<?php echo htmlspecialchars($_POST['stop_name'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <button type="submit" name="add_stop" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Add Stop
            </button>
            <a href="/bus/admin/stops/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

