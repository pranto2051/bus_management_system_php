<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$bus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bus_id > 0) {
    // Check if bus has location data
    $check_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM bus_location WHERE bus_id = ?");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "i", $bus_id);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        $location_count = mysqli_fetch_assoc($result)['count'] ?? 0;
        mysqli_stmt_close($check_stmt);

        if ($location_count > 0) {
            // Delete location data first
            mysqli_query($conn, "DELETE FROM bus_location WHERE bus_id = $bus_id");
        }
    }

    // Delete bus
    $stmt = mysqli_prepare($conn, "DELETE FROM bus WHERE bus_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $bus_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /bus/admin/buses/index.php?message=Bus deleted successfully&success=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

header("Location: /bus/admin/buses/index.php?message=Error deleting bus&success=0");
exit();
?>

