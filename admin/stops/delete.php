<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$stop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($stop_id > 0) {
    // Check if stop is used in route_stops
    $check_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM route_stops WHERE stop_id = ?");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "i", $stop_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $route_count = mysqli_fetch_assoc($result)['count'] ?? 0;
        mysqli_stmt_close($check_stmt);

        if ($route_count > 0) {
            header("Location: /bus/admin/stops/index.php?message=Cannot delete stop: It is used in routes&success=0");
            exit();
        }
    }

    // Delete stop
    $stmt = mysqli_prepare($conn, "DELETE FROM stop WHERE stop_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $stop_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /bus/admin/stops/index.php?message=Stop deleted successfully&success=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

header("Location: /bus/admin/stops/index.php?message=Error deleting stop&success=0");
exit();
?>

