<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_header.php";

$message = "";
$messageClass = "";

if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageClass = isset($_GET['success']) && $_GET['success'] == '1' ? 'alert-success' : 'alert-error';
}

// Fetch latest location per bus
$bus_locations = [];
$sql = "
    SELECT 
        b.bus_id,
        b.bus_num,
        r.route_name,
        bl.location_lat,
        bl.location_lng,
        bl.timestamps
    FROM bus b
    JOIN route r ON r.route_id = b.route_id
    LEFT JOIN (
        SELECT bl1.*
        FROM bus_location bl1
        INNER JOIN (
            SELECT bus_id, MAX(timestamps) as max_time
            FROM bus_location
            GROUP BY bus_id
        ) bl2 ON bl1.bus_id = bl2.bus_id AND bl1.timestamps = bl2.max_time
    ) bl ON bl.bus_id = b.bus_id
    ORDER BY b.bus_num ASC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bus_locations[] = $row;
    }
    mysqli_free_result($result);
}

// Handle manual location update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_location'])) {
    $bus_id = (int)($_POST['bus_id'] ?? 0);
    $lat = (float)($_POST['lat'] ?? 0);
    $lng = (float)($_POST['lng'] ?? 0);

    if ($bus_id > 0 && $lat != 0 && $lng != 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO bus_location (bus_id, location_lat, location_lng) VALUES (?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "idd", $bus_id, $lat, $lng);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /bus/admin/locations/index.php?message=Location updated successfully&success=1");
                exit();
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $message = "Invalid location data";
        $messageClass = "alert-error";
    }
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Bus Locations</h1>
    <p class="admin-page-subtitle">View and manage bus locations</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="<?php echo $messageClass; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Latest Bus Locations</h2>
    </div>

    <?php if (!empty($bus_locations)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Bus Number</th>
                        <th>Route</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bus_locations as $location): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($location['bus_num']); ?></td>
                            <td><?php echo htmlspecialchars($location['route_name']); ?></td>
                            <td><?php echo $location['location_lat'] ? htmlspecialchars($location['location_lat']) : 'N/A'; ?></td>
                            <td><?php echo $location['location_lng'] ? htmlspecialchars($location['location_lng']) : 'N/A'; ?></td>
                            <td><?php echo $location['timestamps'] ? date('Y-m-d H:i:s', strtotime($location['timestamps'])) : 'Never'; ?></td>
                            <td>
                                <?php if ($location['bus_id']): ?>
                                    <button 
                                        type="button" 
                                        class="admin-btn admin-btn-secondary admin-btn-sm" 
                                        onclick="showUpdateForm(<?php echo $location['bus_id']; ?>, '<?php echo htmlspecialchars($location['bus_num']); ?>', <?php echo $location['location_lat'] ? $location['location_lat'] : 0; ?>, <?php echo $location['location_lng'] ? $location['location_lng'] : 0; ?>)"
                                    >
                                        <i class="fa-solid fa-map-marker-alt"></i> Update
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <i class="fa-solid fa-map-location-dot"></i>
            <p>No bus locations found.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Update Location Modal -->
<div id="updateLocationModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div class="admin-card" style="max-width: 500px; margin: 2rem; position: relative;">
        <button onclick="closeUpdateForm()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; color: #0e7803; cursor: pointer;">&times;</button>
        <h2 style="margin-top: 0;">Update Bus Location</h2>
        <form method="POST" id="locationUpdateForm">
            <input type="hidden" name="bus_id" id="update_bus_id">
            <div class="admin-form-group">
                <label class="admin-form-label">Bus Number</label>
                <input type="text" id="update_bus_num" class="admin-form-control" readonly>
            </div>
            <div class="admin-form-group">
                <label for="update_lat" class="admin-form-label">Latitude</label>
                <input type="number" step="0.000001" id="update_lat" name="lat" class="admin-form-control" required>
            </div>
            <div class="admin-form-group">
                <label for="update_lng" class="admin-form-label">Longitude</label>
                <input type="number" step="0.000001" id="update_lng" name="lng" class="admin-form-control" required>
            </div>
            <div class="admin-form-group">
                <button type="submit" name="update_location" class="admin-btn admin-btn-primary">
                    <i class="fa-solid fa-save"></i> Update Location
                </button>
                <button type="button" onclick="closeUpdateForm()" class="admin-btn admin-btn-secondary">
                    <i class="fa-solid fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showUpdateForm(busId, busNum, lat, lng) {
    document.getElementById('update_bus_id').value = busId;
    document.getElementById('update_bus_num').value = busNum;
    document.getElementById('update_lat').value = lat || '';
    document.getElementById('update_lng').value = lng || '';
    document.getElementById('updateLocationModal').style.display = 'flex';
}

function closeUpdateForm() {
    document.getElementById('updateLocationModal').style.display = 'none';
}
</script>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

