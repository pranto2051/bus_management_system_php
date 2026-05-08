<?php
include __DIR__ . "/../includes/db.php";
include __DIR__ . "/../includes/admin_header.php";

// Fetch statistics
$stats = [];

// Total Routes
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM route");
$stats['routes'] = mysqli_fetch_assoc($result)['count'] ?? 0;

// Total Stops
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM stop");
$stats['stops'] = mysqli_fetch_assoc($result)['count'] ?? 0;

// Active Buses
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bus");
$stats['buses'] = mysqli_fetch_assoc($result)['count'] ?? 0;

// Total Drivers
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM driver");
$stats['drivers'] = mysqli_fetch_assoc($result)['count'] ?? 0;

// Total Users
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM user");
$stats['users'] = mysqli_fetch_assoc($result)['count'] ?? 0;

// Active Schedules
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM schedule");
$stats['schedules'] = mysqli_fetch_assoc($result)['count'] ?? 0;

// Fetch recent activity
$recent_activity = [];

// Recent routes (latest 3)
$result = mysqli_query($conn, "SELECT route_id, route_name FROM route ORDER BY route_id DESC LIMIT 3");
while ($row = mysqli_fetch_assoc($result)) {
    $recent_activity[] = ['type' => 'route', 'id' => $row['route_id'], 'name' => $row['route_name'], 'icon' => 'fa-route'];
}

// Recent stops (latest 2)
$result = mysqli_query($conn, "SELECT stop_id, stop_name FROM stop ORDER BY stop_id DESC LIMIT 2");
while ($row = mysqli_fetch_assoc($result)) {
    $recent_activity[] = ['type' => 'stop', 'id' => $row['stop_id'], 'name' => $row['stop_name'], 'icon' => 'fa-map-marker-alt'];
}

// Recent buses (latest 2)
$result = mysqli_query($conn, "SELECT bus_id, bus_num FROM bus ORDER BY bus_id DESC LIMIT 2");
while ($row = mysqli_fetch_assoc($result)) {
    $recent_activity[] = ['type' => 'bus', 'id' => $row['bus_id'], 'name' => $row['bus_num'], 'icon' => 'fa-bus'];
}

// Recent schedules (latest 2)
$result = mysqli_query($conn, "SELECT s.schedule_id, r.route_name FROM schedule s JOIN route r ON r.route_id = s.route_id ORDER BY s.schedule_id DESC LIMIT 2");
while ($row = mysqli_fetch_assoc($result)) {
    $recent_activity[] = ['type' => 'schedule', 'id' => $row['schedule_id'], 'name' => $row['route_name'], 'icon' => 'fa-clock'];
}

// Sort by ID descending and limit to 6
usort($recent_activity, function($a, $b) {
    return $b['id'] - $a['id'];
});
$recent_activity = array_slice($recent_activity, 0, 6);

// Fetch live bus status (buses with schedules that are currently active based on time)
$current_time = date('H:i:s');
$live_buses = [];
// Get buses that have schedules where current time is between departure and arrival, or upcoming within 1 hour
$sql = "
    SELECT 
        b.bus_id,
        b.bus_num,
        r.route_name,
        d.driver_name,
        s.departure_time,
        s.arrival_time,
        CASE 
            WHEN ? BETWEEN s.departure_time AND s.arrival_time THEN 'On Route'
            WHEN ? < s.departure_time AND s.departure_time <= ADDTIME(?, '01:00:00') THEN 'Upcoming'
            ELSE 'Completed'
        END as status
    FROM bus b
    JOIN route r ON r.route_id = b.route_id
    JOIN schedule s ON s.schedule_id = b.schedule_id
    JOIN driver d ON d.driver_id = s.driver_id
    WHERE ? BETWEEN s.departure_time AND s.arrival_time
       OR (? < s.departure_time AND s.departure_time <= ADDTIME(?, '01:00:00'))
    ORDER BY s.departure_time ASC
    LIMIT 5
";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssssss", $current_time, $current_time, $current_time, $current_time, $current_time, $current_time);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $live_buses[] = $row;
    }
    mysqli_stmt_close($stmt);
}

// System info
$system_info = [];
$system_info['php_version'] = PHP_VERSION;
$system_info['db_records'] = $stats['routes'] + $stats['stops'] + $stats['buses'] + $stats['drivers'] + $stats['users'] + $stats['schedules'];
$system_info['server_time'] = date('Y-m-d H:i:s');
$system_info['db_name'] = 'local_bus_monitoring';

// Fetch drivers and users for Security panel
$drivers_list = [];
$result = mysqli_query($conn, "SELECT driver_id, driver_name FROM driver ORDER BY driver_name ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $drivers_list[] = $row;
    }
    mysqli_free_result($result);
}

$users_list = [];
$result = mysqli_query($conn, "SELECT user_id, name, email FROM user ORDER BY name ASC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users_list[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Dashboard</h1>
    <p class="admin-page-subtitle">Overview of your bus monitoring system</p>
</div>

<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-route"></i>
        </div>
        <div class="admin-stat-value"><?php echo htmlspecialchars($stats['routes']); ?></div>
        <p class="admin-stat-label">Total Routes</p>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-map-marker-alt"></i>
        </div>
        <div class="admin-stat-value"><?php echo htmlspecialchars($stats['stops']); ?></div>
        <p class="admin-stat-label">Total Stops</p>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-bus"></i>
        </div>
        <div class="admin-stat-value"><?php echo htmlspecialchars($stats['buses']); ?></div>
        <p class="admin-stat-label">Active Buses</p>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-id-card"></i>
        </div>
        <div class="admin-stat-value"><?php echo htmlspecialchars($stats['drivers']); ?></div>
        <p class="admin-stat-label">Total Drivers</p>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="admin-stat-value"><?php echo htmlspecialchars($stats['users']); ?></div>
        <p class="admin-stat-label">Total Users</p>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="admin-stat-value"><?php echo htmlspecialchars($stats['schedules']); ?></div>
        <p class="admin-stat-label">Active Schedules</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Quick Actions</h2>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 0.75rem;">
        <a href="/bus/admin/routes/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add Route
        </a>
        <a href="/bus/admin/stops/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add Stop
        </a>
        <a href="/bus/admin/schedules/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add Schedule
        </a>
        <a href="/bus/admin/buses/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add Bus
        </a>
        <a href="/bus/admin/drivers/add.php" class="admin-btn admin-btn-primary">
            <i class="fa-solid fa-plus"></i> Add Driver
        </a>
    </div>
</div>

<!-- Recent Activity and Live Bus Status Row -->
<!-- Security shortcut -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Security: Password Management</h2>
    </div>
    <div style="padding:0.75rem; display:flex; gap:0.75rem; align-items:center; justify-content:space-between;">
        <div>
            <p style="margin:0 0 0.5rem 0;">Manage driver and user passwords. Use the dedicated Security page for search and bulk actions.</p>
            <a href="/bus/admin/security.php" class="admin-btn admin-btn-primary"><i class="fa-solid fa-shield-keyhole"></i> Open Security Center</a>
        </div>
        <div style="text-align:right; color:var(--text-muted); font-size:0.9rem;">
            <div>Quick access to search, filter, and reset passwords</div>
        </div>
    </div>
</div>
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
    <!-- Recent Activity -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Recent Activity</h2>
        </div>
        <?php if (!empty($recent_activity)): ?>
            <div class="admin-activity-list">
                <?php foreach ($recent_activity as $activity): ?>
                    <div class="admin-activity-item">
                        <div class="admin-activity-icon">
                            <i class="fa-solid <?php echo htmlspecialchars($activity['icon']); ?>"></i>
                        </div>
                        <div class="admin-activity-content">
                            <span class="admin-activity-text">
                                <?php
                                $type_label = ucfirst($activity['type']);
                                $edit_url = "/bus/admin/" . $activity['type'] . "s/edit.php?id=" . $activity['id'];
                                echo htmlspecialchars($type_label) . " <strong>" . htmlspecialchars($activity['name']) . "</strong> was added";
                                ?>
                            </span>
                        </div>
                        <a href="<?php echo $edit_url; ?>" class="admin-activity-link">
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #7f1d1d; font-size: 0.9rem; margin: 0;">No recent activity</p>
        <?php endif; ?>
    </div>

    <!-- Live Bus Status -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Live Bus Status</h2>
        </div>
        <?php if (!empty($live_buses)): ?>
            <div class="admin-table-wrapper">
                <table class="admin-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Bus</th>
                            <th>Route</th>
                            <th>Driver</th>
                            <th>Schedule</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($live_buses as $bus): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($bus['bus_num']); ?></strong></td>
                                <td><?php echo htmlspecialchars($bus['route_name']); ?></td>
                                <td><?php echo htmlspecialchars($bus['driver_name']); ?></td>
                                <td>
                                    <?php echo date('H:i', strtotime($bus['departure_time'])); ?> - 
                                    <?php echo date('H:i', strtotime($bus['arrival_time'])); ?>
                                </td>
                                <td>
                                    <span class="admin-status-badge admin-status-<?php echo strtolower(str_replace(' ', '-', $bus['status'] ?? 'On Route')); ?>">
                                        <?php echo htmlspecialchars($bus['status'] ?? 'On Route'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: #7f1d1d; font-size: 0.9rem; margin: 0;">No buses currently on route</p>
        <?php endif; ?>
    </div>
</div>

<!-- System Info -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">System Information</h2>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <div class="admin-info-item">
            <div class="admin-info-icon">
                <i class="fa-solid fa-database"></i>
            </div>
            <div class="admin-info-content">
                <span class="admin-info-label">Database</span>
                <span class="admin-info-value"><?php echo htmlspecialchars($system_info['db_name']); ?></span>
            </div>
        </div>
        <div class="admin-info-item">
            <div class="admin-info-icon">
                <i class="fa-solid fa-table"></i>
            </div>
            <div class="admin-info-content">
                <span class="admin-info-label">Total Records</span>
                <span class="admin-info-value"><?php echo number_format($system_info['db_records']); ?></span>
            </div>
        </div>
        <div class="admin-info-item">
            <div class="admin-info-icon">
                <i class="fa-solid fa-code"></i>
            </div>
            <div class="admin-info-content">
                <span class="admin-info-label">PHP Version</span>
                <span class="admin-info-value"><?php echo htmlspecialchars($system_info['php_version']); ?></span>
            </div>
        </div>
        <div class="admin-info-item">
            <div class="admin-info-icon">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="admin-info-content">
                <span class="admin-info-label">Server Time</span>
                <span class="admin-info-value"><?php echo htmlspecialchars($system_info['server_time']); ?></span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../includes/admin_footer.php"; ?>

