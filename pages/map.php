<?php
include __DIR__ . "/../includes/auth.php";
require_login();
include __DIR__ . "/../includes/db.php";
include __DIR__ . "/../includes/header.php";

// Fetch latest location per bus (if any)
$busLocations = [];

$sql = "
    SELECT 
        b.bus_id,
        b.bus_num,
        r.route_name,
        d.driver_name,
        d.phone_number AS driver_phone,
        bl.location_lat,
        bl.location_lng,
        bl.timestamps
    FROM bus_location bl
    JOIN (
        SELECT bus_id, MAX(timestamps) AS max_time
        FROM bus_location
        GROUP BY bus_id
    ) latest ON latest.bus_id = bl.bus_id AND latest.max_time = bl.timestamps
    JOIN bus b ON b.bus_id = bl.bus_id
    JOIN route r ON r.route_id = b.route_id
    LEFT JOIN schedule s ON s.schedule_id = b.schedule_id
    LEFT JOIN driver d ON d.driver_id = s.driver_id
    ORDER BY bl.timestamps DESC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $busLocations[] = $row;
    }
    mysqli_free_result($result);
}
?>

<section class="map-fullscreen container">
    <div class="dashboard-header">
        <div>
            <h2 class="dashboard-title">Live bus map (demo)</h2>
            <p class="dashboard-subtitle">
                This map shows the last known location of each bus from the database.
            </p>
        </div>
        <div class="dashboard-actions">
            <a href="<?php echo BASE_PATH; ?>/pages/dashboard.php" class="link-small">Back to dashboard</a>
        </div>
    </div>

    <div class="map-layout">
        <div class="map-main">
            <div id="bus-map" class="map-container"></div>
        </div>

        <aside class="map-sidebar card">
            <div class="card-header">
                <div>
                    <h3>Buses</h3>
                    <p class="card-subtitle">
                        Last reported position for each bus.
                    </p>
                </div>
            </div>

            <?php if (!empty($busLocations)): ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>Bus</th>
                            <th>Route</th>
                            <th>Driver</th>
                            <th>Last seen</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($busLocations as $bus): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($bus['bus_num']); ?></td>
                                <td><?php echo htmlspecialchars($bus['route_name']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($bus['driver_name'] ?? '—'); ?>
                                    <?php if (!empty($bus['driver_phone'])): ?>
                                        <br><span class="text-muted"><?php echo htmlspecialchars($bus['driver_phone']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($bus['timestamps']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="empty-state">
                    No bus location data found. Seed the database to see sample markers.
                </p>
            <?php endif; ?>
        </aside>
    </div>
</section>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
></script>
<script>
    (function () {
        const mapElement = document.getElementById('bus-map');
        if (!mapElement) return;

        const buses = <?php echo json_encode($busLocations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        // Default center around Dhaka
        let centerLat = 23.780887;
        let centerLng = 90.279239;

        if (buses.length > 0 && buses[0].location_lat && buses[0].location_lng) {
            centerLat = parseFloat(buses[0].location_lat);
            centerLng = parseFloat(buses[0].location_lng);
        }

        const map = L.map('bus-map').setView([centerLat, centerLng], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        buses.forEach(function (bus) {
            const lat = parseFloat(bus.location_lat);
            const lng = parseFloat(bus.location_lng);
            if (!lat || !lng) return;

            const popupHtml =
                '<strong>' + (bus.bus_num ?? 'Bus') + '</strong><br>' +
                'Route: ' + (bus.route_name ?? 'Unknown') + '<br>' +
                'Last seen: ' + (bus.timestamps ?? '');

            L.marker([lat, lng]).addTo(map).bindPopup(popupHtml);
        });
    })();
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>


