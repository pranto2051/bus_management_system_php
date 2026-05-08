<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$driver_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($driver_id > 0) {
    // Check if driver is assigned to schedules
    $check_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM schedule WHERE driver_id = ?");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "i", $driver_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $schedule_count = mysqli_fetch_assoc($result)['count'] ?? 0;
        mysqli_stmt_close($check_stmt);

        if ($schedule_count > 0) {
            header("Location: /bus/admin/drivers/index.php?message=Cannot delete driver: Driver is assigned to schedules&success=0");
            exit();
        }
    }

    // Delete driver
    $stmt = mysqli_prepare($conn, "DELETE FROM driver WHERE driver_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $driver_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /bus/admin/drivers/index.php?message=Driver deleted successfully&success=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

header("Location: /bus/admin/drivers/index.php?message=Error deleting driver&success=0");
exit();
?>

