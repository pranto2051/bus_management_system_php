<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";
$stop = null;

$stop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($stop_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT stop_id, stop_name FROM stop WHERE stop_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $stop_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $stop = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

if (!$stop) {
    header("Location: /bus/admin/stops/index.php?message=Stop not found&success=0");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stop'])) {
    $stop_name = trim($_POST['stop_name'] ?? '');

    if (empty($stop_name)) {
        $message = "Stop name is required";
        $messageClass = "alert-error";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE stop SET stop_name = ? WHERE stop_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $stop_name, $stop_id);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/stops/index.php?message=Stop updated successfully&success=1");
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
    <h1 class="admin-page-title">Edit Stop</h1>
    <p class="admin-page-subtitle">Update stop information</p>
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
                required
                value="<?php echo htmlspecialchars($stop['stop_name']); ?>"
            >
        </div>

        <div class="admin-form-group">
            <button type="submit" name="update_stop" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Update Stop
            </button>
            <a href="/bus/admin/stops/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

