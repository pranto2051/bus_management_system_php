<?php
include __DIR__ . "/../includes/db.php";
include __DIR__ . "/../includes/admin_auth.php";
require_admin_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /bus/admin/dashboard.php');
    exit();
}

$account = $_POST['account'] ?? '';
$generate_temp = isset($_POST['generate_temp']) && $_POST['generate_temp'] == '1';

if (empty($account)) {
    header('Location: /bus/admin/dashboard.php?security_message=' . urlencode('No account selected') . '&success=0');
    exit();
}

$parts = explode('|', $account);
if (count($parts) !== 2) {
    header('Location: /bus/admin/dashboard.php?security_message=' . urlencode('Invalid account selected') . '&success=0');
    exit();
}

$role = $parts[0];
$id = intval($parts[1]);

$new_password = '';
if ($generate_temp) {
    // generate a random temporary password
    $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789!@#%';
    $pw = '';
    for ($i = 0; $i < 10; $i++) { $pw .= $chars[random_int(0, strlen($chars)-1)]; }
    $new_password = $pw;
} else {
    $pw1 = $_POST['new_password'] ?? '';
    $pw2 = $_POST['confirm_password'] ?? '';
    if (empty($pw1) || empty($pw2)) {
        header('Location: /bus/admin/dashboard.php?security_message=' . urlencode('Password fields are required unless generating a temporary password') . '&success=0');
        exit();
    }
    if ($pw1 !== $pw2) {
        header('Location: /bus/admin/dashboard.php?security_message=' . urlencode('Passwords do not match') . '&success=0');
        exit();
    }
    if (strlen($pw1) < 6) {
        header('Location: /bus/admin/dashboard.php?security_message=' . urlencode('Password must be at least 6 characters') . '&success=0');
        exit();
    }
    $new_password = $pw1;
}

$hashed = password_hash($new_password, PASSWORD_DEFAULT);
$updated = false;
$message = '';

if ($role === 'driver') {
    $stmt = mysqli_prepare($conn, "UPDATE driver SET password = ? WHERE driver_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $hashed, $id);
        if (mysqli_stmt_execute($stmt)) { $updated = true; }
        else { $message = mysqli_error($conn); }
        mysqli_stmt_close($stmt);
    } else {
        $message = mysqli_error($conn);
    }
} elseif ($role === 'user') {
    $stmt = mysqli_prepare($conn, "UPDATE user SET password = ? WHERE user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $hashed, $id);
        if (mysqli_stmt_execute($stmt)) { $updated = true; }
        else { $message = mysqli_error($conn); }
        mysqli_stmt_close($stmt);
    } else {
        $message = mysqli_error($conn);
    }
} else {
    $message = 'Unknown role';
}

if ($updated) {
    if ($generate_temp) {
        $msg = 'Temporary password set: ' . $new_password;
    } else {
        $msg = 'Password updated successfully';
    }
    header('Location: /bus/admin/dashboard.php?security_message=' . urlencode($msg) . '&success=1');
    exit();
} else {
    $err = 'Failed to update password';
    if (!empty($message)) { $err .= ': ' . $message; }
    header('Location: /bus/admin/dashboard.php?security_message=' . urlencode($err) . '&success=0');
    exit();
}

?>
