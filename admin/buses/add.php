<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";

// Fetch routes and schedules for dropdowns
$routes = [];
$schedules = [];

$routes_result = mysqli_query($conn, "SELECT route_id, route_name FROM route ORDER BY route_name ASC");
while ($row = mysqli_fetch_assoc($routes_result)) {
    $routes[] = $row;
}

$schedules_result = mysqli_query($conn, "
    SELECT s.schedule_id, s.departure_time, s.arrival_time, r.route_name 
    FROM schedule s 
    JOIN route r ON r.route_id = s.route_id 
    ORDER BY r.route_name, s.departure_time ASC
");
while ($row = mysqli_fetch_assoc($schedules_result)) {
    $schedules[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bus'])) {
    $bus_num = trim($_POST['bus_num'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 0);
    $route_id = (int)($_POST['route_id'] ?? 0);
    $schedule_id = (int)($_POST['schedule_id'] ?? 0);

    if (empty($bus_num) || $capacity <= 0 || $route_id <= 0 || $schedule_id <= 0) {
        $message = "All fields are required and capacity must be greater than 0";
        $messageClass = "alert-error";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO bus (bus_num, capacity, route_id, schedule_id) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "siii", $bus_num, $capacity, $route_id, $schedule_id);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/buses/index.php?message=Bus added successfully&success=1");
                exit();
            } else {
                $message = "Error: " . mysqli_error($conn);
                $messageClass = "alert-error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

include __DIR__ . "/../../includes/admin_header.php";
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Add New Bus</h1>
    <p class="admin-page-subtitle">Register a new bus in the system</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" class="admin-form">
        <div class="admin-form-group">
            <label for="bus_num" class="admin-form-label">Bus Number</label>
            <input
                type="text"
                id="bus_num"
                name="bus_num"
                class="admin-form-control"
                placeholder="e.g., BUS-101"
                required
                value="<?php echo htmlspecialchars($_POST['bus_num'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <label for="capacity" class="admin-form-label">Capacity</label>
            <input
                type="number"
                id="capacity"
                name="capacity"
                class="admin-form-control"
                placeholder="e.g., 40"
                min="1"
                required
                value="<?php echo htmlspecialchars($_POST['capacity'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <label for="route_id" class="admin-form-label">Route</label>
            <select id="route_id" name="route_id" class="admin-form-control admin-form-select" required>
                <option value="">Select a route</option>
                <?php foreach ($routes as $route): ?>
                    <option value="<?php echo $route['route_id']; ?>" <?php echo (isset($_POST['route_id']) && $_POST['route_id'] == $route['route_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($route['route_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="admin-form-group">
            <label for="schedule_id" class="admin-form-label">Schedule</label>
            <select id="schedule_id" name="schedule_id" class="admin-form-control admin-form-select" required>
                <option value="">Select a schedule</option>
                <?php foreach ($schedules as $schedule): ?>
                    <option value="<?php echo $schedule['schedule_id']; ?>" <?php echo (isset($_POST['schedule_id']) && $_POST['schedule_id'] == $schedule['schedule_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($schedule['route_name']); ?> - <?php echo date('H:i', strtotime($schedule['departure_time'])); ?> to <?php echo date('H:i', strtotime($schedule['arrival_time'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="admin-form-group">
            <button type="submit" name="add_bus" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Add Bus
            </button>
            <a href="/bus/admin/buses/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

