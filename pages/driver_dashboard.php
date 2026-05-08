<?php
session_start();
include __DIR__ . "/../includes/db.php";

// Simple driver auth check
if (empty($_SESSION['driver_id'])) {
    header("Location: " . (defined('BASE_PATH') ? BASE_PATH : '') . "/pages/login.php");
    exit();
}

$driver_id = $_SESSION['driver_id'];
$driver_name = $_SESSION['driver_name'] ?? '';

// Fetch assigned schedules for this driver (if any)
$schedules = [];
$sql = "SELECT s.schedule_id, r.route_name, s.departure_time, s.arrival_time, b.bus_num
        FROM schedule s
        LEFT JOIN route r ON r.route_id = s.route_id
        LEFT JOIN bus b ON b.schedule_id = s.schedule_id
        WHERE s.driver_id = ?
        ORDER BY s.departure_time ASC
        LIMIT 20";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $driver_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $schedules[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

include __DIR__ . "/../includes/header.php";
?>
<section class="dashboard container">
    <div class="dashboard-header">
        <div>
            <h2 class="dashboard-title">Driver Dashboard</h2>
            <p class="dashboard-subtitle">Welcome back, <?php echo htmlspecialchars($driver_name); ?></p>
        </div>
        <div class="dashboard-actions">
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/pages/dashboard.php" class="link-small">User Dashboard</a>
            <a href="<?php echo defined('BASE_PATH') ? BASE_PATH : ''; ?>/pages/logout.php" class="link-small">Logout</a>
        </div>
    </div>

    <section class="card">
        <div class="card-header">
            <div>
                <h3>Your Assigned Schedules</h3>
                <p class="card-subtitle">Schedules where you are the assigned driver.</p>
            </div>
        </div>

        <div class="table-wrapper">
            <?php if (!empty($schedules)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Bus</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['route_name']); ?></td>
                                <td><?php echo htmlspecialchars($s['bus_num'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($s['departure_time']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($s['arrival_time']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">No schedules assigned to you yet.</p>
            <?php endif; ?>
        </div>
    </section>
</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>
