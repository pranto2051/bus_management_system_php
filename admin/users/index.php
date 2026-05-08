<?php
include __DIR__ . "/../../includes/db.php";
include __DIR__ . "/../../includes/admin_header.php";

// Fetch all users with favourite routes count
$users = [];
$sql = "
    SELECT 
        u.user_id,
        u.name,
        u.email,
        u.phone_number,
        COUNT(DISTINCT ufr.fav_id) as favourite_count
    FROM user u
    LEFT JOIN user_favourite_route ufr ON ufr.user_id = u.user_id
    GROUP BY u.user_id, u.name, u.email, u.phone_number
    ORDER BY u.name ASC
";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_free_result($result);
}
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Users Management</h1>
    <p class="admin-page-subtitle">View all registered users in the system</p>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">All Users</h2>
    </div>

    <?php if (!empty($users)): ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Favourite Routes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['favourite_count']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <i class="fa-solid fa-users"></i>
            <p>No users found.</p>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/../../includes/admin_footer.php"; ?>

