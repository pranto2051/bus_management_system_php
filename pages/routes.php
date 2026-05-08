<?php
include __DIR__ . "/../includes/auth.php";
require_login();
include __DIR__ . "/../includes/db.php";

$user_id = (int) ($_SESSION['user_id'] ?? 0);

// -------------------------------------------------------------------------
// Handle favourite toggle actions (?fav=route_id, ?unfav=route_id)
// -------------------------------------------------------------------------
if ($user_id > 0 && (isset($_GET['fav']) || isset($_GET['unfav']))) {
    $isUnfav = isset($_GET['unfav']);
    $route_id = (int) ($isUnfav ? $_GET['unfav'] : $_GET['fav']);

    if ($route_id > 0) {
        if ($isUnfav) {
            // Remove from favourites
            if ($stmt = mysqli_prepare($conn, "DELETE FROM user_favourite_route WHERE user_id = ? AND route_id = ?")) {
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $route_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            // Add to favourites if not already present
            if ($stmt = mysqli_prepare($conn, "SELECT 1 FROM user_favourite_route WHERE user_id = ? AND route_id = ?")) {
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $route_id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $exists = $res && mysqli_num_rows($res) > 0;
                mysqli_stmt_close($stmt);

                if (!$exists && ($stmt = mysqli_prepare($conn, "INSERT INTO user_favourite_route (user_id, route_id) VALUES (?, ?)"))) {
                    mysqli_stmt_bind_param($stmt, "ii", $user_id, $route_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    // Redirect back to routes page without fav/unfav parameters
    require_once __DIR__ . "/../includes/config.php";
    $redirectParams = $_GET;
    unset($redirectParams['fav'], $redirectParams['unfav']);
    $redirectUrl = BASE_PATH . '/pages/routes.php';
    if (!empty($redirectParams)) {
        $redirectUrl .= '?' . http_build_query($redirectParams);
    }
    header("Location: " . $redirectUrl);
    exit();
}

// -------------------------------------------------------------------------
// Check if viewing single route details
// -------------------------------------------------------------------------
$viewRouteId = isset($_GET['route_id']) ? (int) $_GET['route_id'] : 0;

if ($viewRouteId > 0) {
    // Fetch detailed route information
    $routeDetails = null;
    $routeStops = [];
    $routeSchedules = [];
    $routeBuses = [];
    
    // Get route basic info
    if ($stmt = mysqli_prepare($conn, "SELECT route_id, route_name FROM route WHERE route_id = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $viewRouteId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && $row = mysqli_fetch_assoc($result)) {
            $routeDetails = $row;
        }
        mysqli_stmt_close($stmt);
    }
    
    if ($routeDetails) {
        // Get stops in order
        if ($stmt = mysqli_prepare($conn, "
            SELECT st.stop_id, st.stop_name, rs.stop_order
            FROM route_stops rs
            JOIN stop st ON st.stop_id = rs.stop_id
            WHERE rs.route_id = ?
            ORDER BY rs.stop_order ASC
        ")) {
            mysqli_stmt_bind_param($stmt, "i", $viewRouteId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $routeStops[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        
        // Get schedules
        if ($stmt = mysqli_prepare($conn, "
            SELECT s.schedule_id, s.departure_time, s.arrival_time, 
                   d.driver_name, d.phone_number as driver_phone,
                   b.bus_id, b.bus_num, b.capacity
            FROM schedule s
            LEFT JOIN driver d ON d.driver_id = s.driver_id
            LEFT JOIN bus b ON b.schedule_id = s.schedule_id
            WHERE s.route_id = ?
            ORDER BY s.departure_time ASC
        ")) {
            mysqli_stmt_bind_param($stmt, "i", $viewRouteId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $routeSchedules[] = $row;
                }
            }
            mysqli_stmt_close($stmt);
        }
        
        // Check if route is favourite
        $isFavourite = false;
        if ($user_id > 0 && $stmt = mysqli_prepare($conn, "SELECT 1 FROM user_favourite_route WHERE user_id = ? AND route_id = ?")) {
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $viewRouteId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $isFavourite = $result && mysqli_num_rows($result) > 0;
            mysqli_stmt_close($stmt);
        }
    }
    
    include __DIR__ . "/../includes/header.php";
    ?>
    
    <section class="dashboard container">
        <div class="dashboard-header">
            <div>
                <h2 class="dashboard-title">Route Details</h2>
                <p class="dashboard-subtitle">
                    Complete information about this bus route.
                </p>
            </div>
            <div class="dashboard-actions">
                <a href="<?php echo BASE_PATH; ?>/pages/routes.php" class="link-small">← Back to all routes</a>
            </div>
        </div>
        
        <?php if ($routeDetails): ?>
            <section class="card">
                <div class="card-header">
                    <div>
                        <h3><?php echo htmlspecialchars($routeDetails['route_name']); ?></h3>
                        <p class="card-subtitle">Route ID: <?php echo (int) $routeDetails['route_id']; ?></p>
                    </div>
                    <div>
                        <?php if ($isFavourite): ?>
                            <a href="<?php echo BASE_PATH; ?>/pages/routes.php?route_id=<?php echo $viewRouteId; ?>&unfav=<?php echo $viewRouteId; ?>" class="btn-pill-small btn-pill-secondary">Remove from Favourites</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_PATH; ?>/pages/routes.php?route_id=<?php echo $viewRouteId; ?>&fav=<?php echo $viewRouteId; ?>" class="btn-pill-small">Add to Favourites</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Stops Section -->
                <div class="card-section">
                    <h4>Stops (in order)</h4>
                    <?php if (!empty($routeStops)): ?>
                        <ol class="route-stops-list">
                            <?php foreach ($routeStops as $index => $stop): ?>
                                <li>
                                    <span class="stop-number"><?php echo $index + 1; ?></span>
                                    <span class="stop-name"><?php echo htmlspecialchars($stop['stop_name']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <p class="empty-state">No stops configured for this route.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Schedules Section -->
                <div class="card-section">
                    <h4>Schedules</h4>
                    <?php if (!empty($routeSchedules)): ?>
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                        <th>Duration</th>
                                        <th>Driver</th>
                                        <th>Bus</th>
                                        <th>Capacity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($routeSchedules as $schedule): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(substr($schedule['departure_time'], 0, 5)); ?></td>
                                            <td><?php echo htmlspecialchars(substr($schedule['arrival_time'], 0, 5)); ?></td>
                                            <td>
                                                <?php
                                                $dep = new DateTime($schedule['departure_time']);
                                                $arr = new DateTime($schedule['arrival_time']);
                                                $diff = $dep->diff($arr);
                                                echo $diff->h . 'h ' . $diff->i . 'm';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($schedule['driver_name'])): ?>
                                                    <?php echo htmlspecialchars($schedule['driver_name']); ?>
                                                    <br><small><?php echo htmlspecialchars($schedule['driver_phone']); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($schedule['bus_num'])): ?>
                                                    <?php echo htmlspecialchars($schedule['bus_num']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($schedule['capacity'])): ?>
                                                    <?php echo (int) $schedule['capacity']; ?> seats
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="empty-state">No schedules available for this route.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php else: ?>
            <section class="card">
                <p class="empty-state">Route not found.</p>
                <a href="<?php echo BASE_PATH; ?>/pages/routes.php" class="btn-primary">Back to Routes</a>
            </section>
        <?php endif; ?>
    </section>
    
    <?php include __DIR__ . "/../includes/footer.php"; ?>
    <?php exit(); ?>
<?php
}

// -------------------------------------------------------------------------
// Search and fetch routes with stops and schedule summary (LIST VIEW)
// -------------------------------------------------------------------------
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$routes = [];

$sql = "
    SELECT 
        r.route_id,
        r.route_name,
        GROUP_CONCAT(DISTINCT st.stop_name ORDER BY rs.stop_order SEPARATOR ' → ') AS stops,
        MIN(s.departure_time) AS first_departure,
        MAX(s.departure_time) AS last_departure
    FROM route r
    LEFT JOIN route_stops rs ON rs.route_id = r.route_id
    LEFT JOIN stop st ON st.stop_id = rs.stop_id
    LEFT JOIN schedule s ON s.route_id = r.route_id
    WHERE 1 = 1
";

$types = "";
$params = [];

if ($search !== '') {
    $sql .= " AND (r.route_name LIKE ? OR st.stop_name LIKE ?)";
    $like = "%" . $search . "%";
    $types .= "ss";
    $params[] = &$like;
    $params[] = &$like;
}

$sql .= "
    GROUP BY r.route_id, r.route_name
    ORDER BY r.route_name ASC
";

if ($stmt = mysqli_prepare($conn, $sql)) {
    if (!empty($params)) {
        array_unshift($params, $types);
        call_user_func_array([$stmt, 'bind_param'], $params);
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

// Fetch user's favourite route ids for quick lookup
$favouriteRouteIds = [];
if ($user_id > 0 && $stmt = mysqli_prepare($conn, "SELECT route_id FROM user_favourite_route WHERE user_id = ?")) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $favouriteRouteIds[(int) $row['route_id']] = true;
        }
    }
    mysqli_stmt_close($stmt);
}

include __DIR__ . "/../includes/header.php";
?>

<section class="dashboard container">
    <div class="dashboard-header">
        <div>
            <h2 class="dashboard-title">Browse bus routes</h2>
            <p class="dashboard-subtitle">
                View all available routes, their key stops, and schedule overview.
            </p>
        </div>
        <div class="dashboard-actions">
            <a href="<?php echo BASE_PATH; ?>/pages/dashboard.php" class="link-small">Back to dashboard</a>
        </div>
    </div>

    <section class="card">
        <div class="card-header">
            <div>
                <h3>Available routes</h3>
                <p class="card-subtitle">
                    Search by route name or stop name to quickly find a route.
                </p>
            </div>
        </div>

        <form method="get" class="dashboard-search">
            <div class="form-group-inline">
                <label for="q">Search routes or stops</label>
                <input
                    type="text"
                    id="q"
                    name="q"
                    class="form-control"
                    placeholder="e.g. Campus, City Center, Airport"
                    value="<?php echo htmlspecialchars($search); ?>"
                >
            </div>
            <div class="dashboard-search-actions">
                <button type="submit" class="btn-primary">Search</button>
                <a href="<?php echo BASE_PATH; ?>/pages/routes.php" class="link-small">Clear</a>
            </div>
        </form>

        <div class="table-wrapper">
            <?php if (!empty($routes)): ?>
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>Route</th>
                        <th>Stops</th>
                        <th>First departure</th>
                        <th>Last departure</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($routes as $route): ?>
                        <tr>
                            <td>
                                    <a href="<?php echo BASE_PATH; ?>/pages/routes.php?route_id=<?php echo (int) $route['route_id']; ?>" class="route-link">
                                        <i class="fa-solid fa-route"></i>
                                        <?php echo htmlspecialchars($route['route_name']); ?>
                                    </a>
                            </td>
                            <td><?php echo htmlspecialchars($route['stops'] ?? '—'); ?></td>
                            <td>
                                <?php echo !empty($route['first_departure']) ? htmlspecialchars(substr($route['first_departure'], 0, 5)) : '-'; ?>
                            </td>
                            <td>
                                <?php echo !empty($route['last_departure']) ? htmlspecialchars(substr($route['last_departure'], 0, 5)) : '-'; ?>
                            </td>
                            <td>
                                <div class="route-actions">
                                    <a href="<?php echo BASE_PATH; ?>/pages/routes.php?route_id=<?php echo (int) $route['route_id']; ?>" class="btn-pill-small">
                                        <i class="fa-solid fa-eye"></i> View Details
                                    </a>
                                    <?php $isFav = isset($favouriteRouteIds[(int) $route['route_id']]); ?>
                                    <?php if ($isFav): ?>
                                        <a
                                            href="<?php echo BASE_PATH; ?>/pages/routes.php?<?php
                                                $params = $_GET;
                                                $params['unfav'] = (int) $route['route_id'];
                                                echo htmlspecialchars(http_build_query($params));
                                            ?>"
                                            class="btn-pill-small btn-pill-secondary"
                                            title="Remove from favourites"
                                        >
                                            <i class="fa-solid fa-heart-circle-xmark"></i>
                                        </a>
                                    <?php else: ?>
                                        <a
                                            href="<?php echo BASE_PATH; ?>/pages/routes.php?<?php
                                                $params = $_GET;
                                                $params['fav'] = (int) $route['route_id'];
                                                echo htmlspecialchars(http_build_query($params));
                                            ?>"
                                            class="btn-pill-small"
                                            title="Add to favourites"
                                        >
                                            <i class="fa-regular fa-heart"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">
                    No routes match your search. Try another stop name or clear the filter.
                </p>
            <?php endif; ?>
        </div>
    </section>
</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>


