<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$route_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($route_id > 0) {
    // Check if route is used in schedules or buses
    $check_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM schedule WHERE route_id = ?");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "i", $route_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $schedule_count = mysqli_fetch_assoc($result)['count'] ?? 0;
        mysqli_stmt_close($check_stmt);

        if ($schedule_count > 0) {
            header("Location: /bus/admin/routes/index.php?message=Cannot delete route: It is used in schedules&success=0");
            exit();
        }
    }

    // Delete route_stops first (foreign key constraint)
    mysqli_query($conn, "DELETE FROM route_stops WHERE route_id = $route_id");

    // Delete route
    $stmt = mysqli_prepare($conn, "DELETE FROM route WHERE route_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $route_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /bus/admin/routes/index.php?message=Route deleted successfully&success=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

header("Location: /bus/admin/routes/index.php?message=Error deleting route&success=0");
exit();
?>

