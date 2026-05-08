<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = '';
$messageClass = '';

$id = intval($_GET['id'] ?? ($_POST['id'] ?? 0));
if ($id <= 0) {
    header('Location: /bus/admin/users/index.php');
    exit();
}

// Fetch user
$user = null;
if ($stmt = mysqli_prepare($conn, "SELECT user_id, name, email, phone_number FROM user WHERE user_id = ? LIMIT 1")) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);
    }
    mysqli_stmt_close($stmt);
}

if (!$user) {
    header('Location: /bus/admin/users/index.php?message=' . urlencode('User not found'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone_number'] ?? '');
    $newPassword = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        $message = 'Name and email are required';
        $messageClass = 'alert-error';
    } else {
        if ($newPassword !== '') {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE user SET name = ?, email = ?, phone_number = ?, password = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssi', $name, $email, $phone, $hashed, $id);
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: /bus/admin/users/index.php?message=' . urlencode('User updated successfully') . '&success=1');
                    exit();
                } else {
                    $message = 'DB error: ' . mysqli_error($conn);
                    $messageClass = 'alert-error';
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $sql = "UPDATE user SET name = ?, email = ?, phone_number = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $phone, $id);
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: /bus/admin/users/index.php?message=' . urlencode('User updated successfully') . '&success=1');
                    exit();
                } else {
                    $message = 'DB error: ' . mysqli_error($conn);
                    $messageClass = 'alert-error';
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

include __DIR__ . "/../../includes/admin_header.php";
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Edit User</h1>
    <p class="admin-page-subtitle">Update user details and optionally reset password</p>
</div>

<?php if (!empty($message)): ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <input type="hidden" name="id" value="<?php echo (int)$user['user_id']; ?>">

        <div class="admin-form-group">
            <label for="name" class="admin-form-label">Name</label>
            <input type="text" id="name" name="name" class="admin-form-control" required value="<?php echo htmlspecialchars($user['name']); ?>">
        </div>

        <div class="admin-form-group">
            <label for="email" class="admin-form-label">Email</label>
            <input type="email" id="email" name="email" class="admin-form-control" required value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>

        <div class="admin-form-group">
            <label for="phone_number" class="admin-form-label">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" class="admin-form-control" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
        </div>

        <div class="admin-form-group">
            <label for="password" class="admin-form-label">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" class="admin-form-control" placeholder="Enter new password">
        </div>

        <div class="admin-form-group">
            <button type="submit" name="save_user" class="admin-btn admin-btn-primary">Save Changes</button>
            <a href="/bus/admin/users/index.php" class="admin-btn admin-btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>
