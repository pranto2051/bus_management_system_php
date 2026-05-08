<?php
session_start();

// Unset admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

// Destroy session
session_destroy();

// Redirect to admin login
header("Location: /bus/admin/login.php");
exit();
?>

