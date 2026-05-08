<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_driver'])) {
    $driver_name = trim($_POST['driver_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($driver_name) || empty($phone_number) || empty($password)) {
        $message = "All fields are required";
        $messageClass = "alert-error";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO driver (driver_name, phone_number, password) VALUES (?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $driver_name, $phone_number, $hashed_password);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/drivers/index.php?message=Driver added successfully&success=1");
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
    <h1 class="admin-page-title">Add New Driver</h1>
    <p class="admin-page-subtitle">Register a new driver</p>
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
                placeholder="e.g., Rahim Uddin"
                required
                value="<?php echo htmlspecialchars($_POST['driver_name'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <label for="phone_number" class="admin-form-label">Phone Number</label>
            <input
                type="text"
                id="phone_number"
                name="phone_number"
                class="admin-form-control"
                placeholder="e.g., 01711111111"
                required
                value="<?php echo htmlspecialchars($_POST['phone_number'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <label for="password" class="admin-form-label">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="admin-form-control"
                placeholder="Enter password"
                required
            >
        </div>

        <div class="admin-form-group">
            <button type="submit" name="add_driver" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Add Driver
            </button>
            <a href="/bus/admin/drivers/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

