<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_auth.php";
require_admin_login();

$message = "";
$messageClass = "";

// Fetch routes and drivers for dropdowns
$routes = [];
$drivers = [];

$routes_result = mysqli_query($conn, "SELECT route_id, route_name FROM route ORDER BY route_name ASC");
while ($row = mysqli_fetch_assoc($routes_result)) {
    $routes[] = $row;
}

$drivers_result = mysqli_query($conn, "SELECT driver_id, driver_name FROM driver ORDER BY driver_name ASC");
while ($row = mysqli_fetch_assoc($drivers_result)) {
    $drivers[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $route_id = (int)($_POST['route_id'] ?? 0);
    $driver_id = (int)($_POST['driver_id'] ?? 0);
    $departure_time = trim($_POST['departure_time'] ?? '');
    $arrival_time = trim($_POST['arrival_time'] ?? '');
    $admin_id = $_SESSION['admin_id'];

    if (empty($route_id) || empty($driver_id) || empty($departure_time) || empty($arrival_time)) {
        $message = "All fields are required";
        $messageClass = "alert-error";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO schedule (route_id, driver_id, admin_id, departure_time, arrival_time) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iiiss", $route_id, $driver_id, $admin_id, $departure_time, $arrival_time);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/schedules/index.php?message=Schedule added successfully&success=1");
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
    <h1 class="admin-page-title">Add New Schedule</h1>
    <p class="admin-page-subtitle">Create a new bus schedule</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" class="admin-form">
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
            <label for="driver_id" class="admin-form-label">Driver</label>
            <select id="driver_id" name="driver_id" class="admin-form-control admin-form-select" required>
                <option value="">Select a driver</option>
                <?php foreach ($drivers as $driver): ?>
                    <option value="<?php echo $driver['driver_id']; ?>" <?php echo (isset($_POST['driver_id']) && $_POST['driver_id'] == $driver['driver_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($driver['driver_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="admin-form-group">
            <label for="departure_time" class="admin-form-label">Departure Time</label>
            <input
                type="time"
                id="departure_time"
                name="departure_time"
                class="admin-form-control"
                required
                value="<?php echo htmlspecialchars($_POST['departure_time'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <label for="arrival_time" class="admin-form-label">Arrival Time</label>
            <input
                type="time"
                id="arrival_time"
                name="arrival_time"
                class="admin-form-control"
                required
                value="<?php echo htmlspecialchars($_POST['arrival_time'] ?? ''); ?>"
            >
        </div>

        <div class="admin-form-group">
            <button type="submit" name="add_schedule" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-save"></i> Add Schedule
            </button>
            <a href="/bus/admin/schedules/index.php" class="admin-btn admin-btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

