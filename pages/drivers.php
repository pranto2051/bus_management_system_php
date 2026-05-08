<?php
include __DIR__ . "/../includes/auth.php";
require_login();
include __DIR__ . "/../includes/db.php";

$drivers = [];
if ($stmt = mysqli_prepare($conn, "SELECT driver_id, driver_name, phone_number FROM driver ORDER BY driver_name ASC")) {
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $drivers[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

include __DIR__ . "/../includes/header.php";
?>

<section class="dashboard container">
    <div class="dashboard-header">
        <div>
            <h2 class="dashboard-title">Drivers</h2>
            <p class="dashboard-subtitle">List of drivers and contact details.</p>
        </div>
        <div class="dashboard-actions">
            <a href="<?php echo BASE_PATH; ?>/pages/dashboard.php" class="link-small">Back to dashboard</a>
        </div>
    </div>

    <section class="card">
        <div class="card-header">
            <div>
                <h3>Available drivers</h3>
                <p class="card-subtitle">Contact information for drivers assigned to schedules.</p>
            </div>
        </div>

        <div class="table-wrapper">
            <?php if (!empty($drivers)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($drivers as $d): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d['driver_name']); ?></td>
                                <td><?php echo htmlspecialchars($d['phone_number']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">No drivers found.</p>
            <?php endif; ?>
        </div>
    </section>
</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>
