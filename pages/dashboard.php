<?php
include __DIR__ . "/../includes/auth.php";
require_login();
include __DIR__ . "/../includes/header.php";
include __DIR__ . "/../includes/db.php";

// Basic search parameters for routes
$search_origin = isset($_GET['origin']) ? trim($_GET['origin']) : '';
$search_destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';

// Fetch profile info for logged in user
$user_id = $_SESSION['user_id'] ?? null;
$userProfile = null;

if ($user_id !== null) {
    $stmt = mysqli_prepare($conn, "SELECT name, email, phone_number FROM user WHERE user_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $userProfile = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch available routes (optionally filter by origin/destination via stop names)
$routes = [];
$routes_sql = "
    SELECT DISTINCT r.route_id, r.route_name,
           MIN(s.departure_time) AS first_departure,
           MAX(s.arrival_time) AS last_arrival
    FROM route r
    LEFT JOIN schedule s ON s.route_id = r.route_id
    WHERE 1 = 1
";

$params = [];
$types = "";

// If both origin and destination are provided, find routes where origin comes before destination
if ($search_origin !== '' && $search_destination !== '') {
    $routes_sql .= "
        AND r.route_id IN (
            SELECT DISTINCT rs1.route_id
            FROM route_stops rs1
            JOIN stop st1 ON st1.stop_id = rs1.stop_id
            JOIN route_stops rs2 ON rs2.route_id = rs1.route_id
            JOIN stop st2 ON st2.stop_id = rs2.stop_id
            WHERE st1.stop_name LIKE ? 
            AND st2.stop_name LIKE ?
            AND rs1.stop_order < rs2.stop_order
        )
    ";
    $like_origin = "%" . $search_origin . "%";
    $like_destination = "%" . $search_destination . "%";
    $params[] = &$like_origin;
    $params[] = &$like_destination;
    $types .= "ss";
} elseif ($search_origin !== '') {
    // Only origin provided
    $routes_sql .= "
        AND r.route_id IN (
            SELECT DISTINCT rs.route_id
            FROM route_stops rs
            JOIN stop st ON st.stop_id = rs.stop_id
            WHERE st.stop_name LIKE ?
        )
    ";
    $like_origin = "%" . $search_origin . "%";
    $params[] = &$like_origin;
    $types .= "s";
} elseif ($search_destination !== '') {
    // Only destination provided
    $routes_sql .= "
        AND r.route_id IN (
            SELECT DISTINCT rs.route_id
            FROM route_stops rs
            JOIN stop st ON st.stop_id = rs.stop_id
            WHERE st.stop_name LIKE ?
        )
    ";
    $like_destination = "%" . $search_destination . "%";
    $params[] = &$like_destination;
    $types .= "s";
}

$routes_sql .= " GROUP BY r.route_id, r.route_name ORDER BY r.route_name ASC LIMIT 20";

if ($stmt = mysqli_prepare($conn, $routes_sql)) {
    if (!empty($params)) {
        array_unshift($params, $types);
        call_user_func_array(
            [$stmt, 'bind_param'],
            $params
        );
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $routes[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

// Helper to simulate arrival status text for last_arrival time
function format_arrival_status(?string $time): string
{
    if (empty($time)) {
        return '';
    }

    $now = new DateTime('now');
    $arrival = DateTime::createFromFormat('H:i:s', $time);
    if (!$arrival) {
        return '';
    }

    // Assume today; if arrival is earlier than now by more than 12 hours, treat as next day
    if ($arrival < $now && $now->getTimestamp() - $arrival->getTimestamp() > 12 * 3600) {
        $arrival->modify('+1 day');
    }

    $diffMinutes = (int) round(($arrival->getTimestamp() - $now->getTimestamp()) / 60);

    if ($diffMinutes > 60) {
        $hours = (int) floor($diffMinutes / 60);
        return "in approx {$hours} hr";
    }
    if ($diffMinutes > 1) {
        return "in {$diffMinutes} min";
    }
    if ($diffMinutes >= 0) {
        return "now";
    }

    // Already passed
    $past = abs($diffMinutes);
    if ($past > 60) {
        $hours = (int) floor($past / 60);
        return "{$hours} hr ago";
    }
    return "{$past} min ago";
}

// Fetch favourite routes for this user
$favouriteRoutes = [];
if ($user_id !== null) {
    $fav_sql = "
        SELECT r.route_id, r.route_name,
               MIN(s.departure_time) AS first_departure,
               MAX(s.arrival_time) AS last_arrival
        FROM user_favourite_route uf
        JOIN route r ON r.route_id = uf.route_id
        LEFT JOIN schedule s ON s.route_id = r.route_id
        WHERE uf.user_id = ?
        GROUP BY r.route_id, r.route_name
        ORDER BY r.route_name ASC
        LIMIT 10
    ";
    if ($stmt = mysqli_prepare($conn, $fav_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $favouriteRoutes[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Bookings system disabled in this version (no booking table)
$userBookings = [];

// Live bus snapshot from schedule (dummy simulation, no GPS)
$liveBuses = [];
$schedule_sql = "
    SELECT 
        s.departure_time,
        s.arrival_time,
        r.route_name,
        b.bus_num,
        d.driver_name
    FROM schedule s
    JOIN route r ON r.route_id = s.route_id
    JOIN bus b ON b.schedule_id = s.schedule_id
    LEFT JOIN driver d ON d.driver_id = s.driver_id
    ORDER BY s.departure_time ASC
    LIMIT 5
";

if ($result = mysqli_query($conn, $schedule_sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $liveBuses[] = $row;
    }
    mysqli_free_result($result);
}

// Simple static notifications for now
$notifications = [
    [
        "title" => "New schedule update",
        "body" => "Morning campus buses will run every 20 minutes from 8:00 AM to 10:30 AM.",
        "type" => "info"
    ],
    [
        "title" => "Route maintenance",
        "body" => "Route 2 (City Center to Airport) may have delays due to road maintenance.",
        "type" => "warning"
    ]
];
?>

<section class="dashboard container">
    <div class="dashboard-header">
        <div>
            <h2 class="dashboard-title">
                Welcome, <?php echo htmlspecialchars($userProfile['name'] ?? ($_SESSION['user_name'] ?? 'User')); ?>
            </h2>
            <p class="dashboard-subtitle">
                Quickly check available routes, your favourites and recent trips.
            </p>
        </div>
        <div class="dashboard-actions">
            <a href="<?php echo BASE_PATH; ?>/pages/routes.php" class="btn-primary dashboard-primary-btn">Browse all routes</a>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Left column: live snapshot + routes + bookings -->
        <div class="dashboard-main">
            <!-- Live bus info (schedule-based dummy snapshot) -->
            <section class="card">
                <div class="card-header">
                    <div>
                        <h3><i class="fa-solid fa-bus"></i> Live bus info</h3>
                        <p class="card-subtitle">Snapshot from today's schedule (simulated real-time, no GPS yet).</p>
                    </div>
                </div>
                <?php if (!empty($liveBuses)): ?>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                            <tr>
                                <th>Bus</th>
                                <th>Route</th>
                                <th>Dep</th>
                                <th>Arr</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($liveBuses as $bus): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bus['bus_num']); ?></td>
                                    <td><?php echo htmlspecialchars($bus['route_name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($bus['departure_time'], 0, 5)); ?></td>
                                    <td><?php echo htmlspecialchars(substr($bus['arrival_time'], 0, 5)); ?></td>
                                    <td class="live-status-cell"
                                        data-dep="<?php echo htmlspecialchars($bus['departure_time']); ?>"
                                        data-arr="<?php echo htmlspecialchars($bus['arrival_time']); ?>">
                                        <span class="live-status-text">-</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="empty-state">
                        Live tracking is not enabled yet. Once real-time data is connected, you will see next bus arrivals here.
                    </p>
                <?php endif; ?>
            </section>

            <!-- Available Routes -->
            <section class="card">
                <div class="card-header">
                    <div>
                        <h3>Available routes</h3>
                        <p class="card-subtitle">Search by stop name to quickly find a route.</p>
                    </div>
                </div>

                <form method="get" class="dashboard-search">
                    <div class="form-group-inline">
                        <label for="origin">Origin stop</label>
                        <input
                            type="text"
                            id="origin"
                            name="origin"
                            class="form-control"
                            placeholder="e.g. Campus Gate"
                            value="<?php echo htmlspecialchars($search_origin); ?>"
                        >
                    </div>
                    <div class="form-group-inline">
                        <label for="destination">Destination</label>
                        <input
                            type="text"
                            id="destination"
                            name="destination"
                            class="form-control"
                            placeholder="e.g. City Center"
                            value="<?php echo htmlspecialchars($search_destination); ?>"
                        >
                    </div>
                    <div class="dashboard-search-actions">
                        <button type="submit" class="btn-primary">Search routes</button>
                        <a href="<?php echo BASE_PATH; ?>/pages/dashboard.php" class="link-small">Clear</a>
                    </div>
                </form>

                <div class="table-wrapper">
                    <?php if (!empty($routes)): ?>
                        <table class="data-table">
                            <thead>
                            <tr>
                                <th>Route name</th>
                                <th>First departure</th>
                                <th>Last arrival</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($routes as $route): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($route['route_name']); ?></td>
                                    <td>
                                        <?php echo $route['first_departure'] ? htmlspecialchars(substr($route['first_departure'], 0, 5)) : '-'; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($route['last_arrival'])): ?>
                                            <?php
                                            $timeLabel = htmlspecialchars(substr($route['last_arrival'], 0, 5));
                                            $status = format_arrival_status($route['last_arrival']);
                                            ?>
                                            <?php echo $timeLabel; ?>
                                            <?php if ($status !== ''): ?>
                                                <br><span class="text-muted"><?php echo htmlspecialchars($status); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo BASE_PATH; ?>/pages/routes.php?route_id=<?php echo (int)$route['route_id']; ?>" class="link-small">
                                            View details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="empty-state">
                            No routes found. Try adjusting your search or visit the
                            <a href="<?php echo BASE_PATH; ?>/pages/routes.php" class="link-small">full routes page</a>.
                        </p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- My trips (bookings disabled) -->
            <section class="card">
                <div class="card-header">
                    <div>
                        <h3>My trips</h3>
                        <p class="card-subtitle">The booking system is not available in this version.</p>
                    </div>
                </div>
                <p class="empty-state">
                    Trip booking and history will be added in a future update. For now, you can browse routes and favourites.
                </p>
            </section>
        </div>

        <!-- Right column: favourites + notifications -->
        <aside class="dashboard-sidebar">
            <!-- Favourite routes -->
            <section class="card">
                <div class="card-header">
                    <div>
                        <h3><i class="fa-solid fa-heart"></i> Favourite routes</h3>
                        <p class="card-subtitle">Your saved routes for quick access.</p>
                    </div>
                </div>

                <?php if (!empty($favouriteRoutes)): ?>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                            <tr>
                                <th>Route</th>
                                <th>First dep.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($favouriteRoutes as $fav): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo BASE_PATH; ?>/pages/routes.php?route_id=<?php echo (int)$fav['route_id']; ?>" class="link-small">
                                            <?php echo htmlspecialchars($fav['route_name']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $fav['first_departure'] ? htmlspecialchars(substr($fav['first_departure'], 0, 5)) : '-'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="empty-state">
                        You have no favourite routes yet. In the future, you will be able to save routes from the routes page.
                    </p>
                <?php endif; ?>
            </section>

            <!-- Notifications -->
            <section class="card notifications-card">
                <div class="card-header">
                    <div>
                        <h3><i class="fa-solid fa-bell"></i> Notifications</h3>
                        <p class="card-subtitle">Stay updated about important route changes.</p>
                    </div>
                </div>

                <?php if (!empty($notifications)): ?>
                    <ul class="notification-list">
                        <?php foreach ($notifications as $note): ?>
                            <li class="notification-item notification-<?php echo htmlspecialchars($note['type']); ?>">
                                <h4><?php echo htmlspecialchars($note['title']); ?></h4>
                                <p><?php echo htmlspecialchars($note['body']); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="empty-state">No new notifications right now.</p>
                <?php endif; ?>
            </section>
        </aside>
    </div>
...</section>

<script>
    (function () {
        const cells = document.querySelectorAll('.live-status-cell');
        if (!cells.length) return;

        function computeStatus(depStr, arrStr, now) {
            if (!depStr || !arrStr) return '';

            function parseHM(time) {
                const parts = time.split(':');
                if (parts.length < 2) return null;
                return {h: parseInt(parts[0], 10), m: parseInt(parts[1], 10)};
            }

            const dep = parseHM(depStr);
            const arr = parseHM(arrStr);
            if (!dep || !arr) return '';

            const base = new Date(now);
            const depTime = new Date(base);
            depTime.setHours(dep.h, dep.m, 0, 0);
            const arrTime = new Date(base);
            arrTime.setHours(arr.h, arr.m, 0, 0);

            const depDiffMin = Math.round((depTime - base) / 60000);
            const arrDiffMin = Math.round((arrTime - base) / 60000);

            if (depDiffMin > 5) {
                return `Starts in ${depDiffMin} min`;
            }
            if (depDiffMin >= -5 && depDiffMin <= 5) {
                return 'Departing now';
            }
            if (depDiffMin < 0 && arrDiffMin > 0) {
                return 'On route';
            }
            if (arrDiffMin <= 0) {
                const past = Math.abs(arrDiffMin);
                if (past > 60) {
                    const hours = Math.floor(past / 60);
                    return `Arrived ${hours} hr ago`;
                }
                return `Arrived ${past} min ago`;
            }
            return '';
        }

        function updateStatuses() {
            const now = new Date();
            cells.forEach((cell) => {
                const dep = cell.getAttribute('data-dep') || '';
                const arr = cell.getAttribute('data-arr') || '';
                const textSpan = cell.querySelector('.live-status-text');
                if (!textSpan) return;
                const status = computeStatus(dep, arr, now);
                textSpan.textContent = status || '-';
            });
        }

        updateStatuses();
        setInterval(updateStatuses, 30000);
    })();
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>

