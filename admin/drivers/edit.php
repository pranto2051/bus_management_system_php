<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";
$driver = null;

$driver_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($driver_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT driver_id, driver_name, phone_number FROM driver WHERE driver_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $driver_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $driver = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

if (!$driver) {
    header("Location: /bus/admin/drivers/index.php?message=Driver not found&success=0");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_driver'])) {
    $driver_name = trim($_POST['driver_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($driver_name) || empty($phone_number)) {
        $message = "Driver name and phone number are required";
        $messageClass = "alert-error";
    } else {
        if (!empty($password)) {
            // Update with new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE driver SET driver_name = ?, phone_number = ?, password = ? WHERE driver_id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssi", $driver_name, $phone_number, $hashed_password, $driver_id);
            }
        } else {
            // Update without password
            $stmt = mysqli_prepare($conn, "UPDATE driver SET driver_name = ?, phone_number = ? WHERE driver_id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssi", $driver_name, $phone_number, $driver_id);
            }
        }

        if ($stmt && mysqli_stmt_execute($stmt)) {
            header("Location: /bus/admin/drivers/index.php?message=Driver updated successfully&success=1");
            exit();
        } else {
            $message = "Error: " . mysqli_error($conn);
            $messageClass = "alert-error";
        }
        if ($stmt) {
            mysqli_stmt_close($stmt);
        }
    }
}

include __DIR__ . "/../../includes/admin_header.php";
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Edit Driver</h1>
    <p class="admin-page-subtitle">Update driver information</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <div class="admin-form-group">
            <label for="driver_name" class="admin-form-label">Driver Name</label>
            <input
                type="text"
                id="driver_name"
                name="driver_name"
                class="admin-form-control"
                required
                value="<?php echo htmlspecialchars($driver['driver_name']); ?>"
            >
        </div>

        <div class="admin-form-group">
            <label for="phone_number" class="admin-form-label">Phone Number</label>
            <input
                type="text"
                id="phone_number"
                name="phone_number"
                class="admin-form-control"
                required
                value="<?php echo htmlspecialchars($driver['phone_number']); ?>"
            >
        </div>

        <div class="admin-form-group">
            <label for="password" class="admin-form-label">New Password (leave blank to keep current)</label>
            <input
                type="password"
                id="password"
                name="password"
                class="admin-form-control"
                placeholder="Enter new password (optional)"
            >
        </div>

        <div class="admin-form-group">
            <button type="submit" name="update_driver" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Update Driver
            </button>
            <a href="/bus/admin/drivers/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

