<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$schedule_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($schedule_id > 0) {
    // Check if schedule is used in buses
    $check_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM bus WHERE schedule_id = ?");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "i", $schedule_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $bus_count = mysqli_fetch_assoc($result)['count'] ?? 0;
        mysqli_stmt_close($check_stmt);

        if ($bus_count > 0) {
            header("Location: /bus/admin/schedules/index.php?message=Cannot delete schedule: It is assigned to buses&success=0");
            exit();
        }
    }

    // Delete schedule
    $stmt = mysqli_prepare($conn, "DELETE FROM schedule WHERE schedule_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /bus/admin/schedules/index.php?message=Schedule deleted successfully&success=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

header("Location: /bus/admin/schedules/index.php?message=Error deleting schedule&success=0");
exit();
?>

