<?php
include __DIR__ . "/../includes/db.php";
include __DIR__ . "/../includes/admin_auth.php";
require_admin_login();

$q = trim($_GET['q'] ?? '');
$role = $_GET['role'] ?? 'all'; // all, driver, user
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$results = [];
$total = 0;

if ($role === 'driver') {
    $sql = "SELECT SQL_CALC_FOUND_ROWS driver_id as id, driver_name as name, phone_number as contact, 'driver' as role FROM driver WHERE 1=1";
    if ($q !== '') {
        $sql .= " AND (driver_name LIKE ? OR phone_number LIKE ? )";
    }
    $sql .= " ORDER BY driver_name ASC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if ($q !== '') {
            $like = "%$q%";
            mysqli_stmt_bind_param($stmt, 'ssii', $like, $like, $perPage, $offset);
        } else {
            mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
        }
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) { $results[] = $row; }
        mysqli_stmt_close($stmt);
        $r = mysqli_query($conn, "SELECT FOUND_ROWS() AS cnt");
        $total = intval(mysqli_fetch_assoc($r)['cnt'] ?? 0);
    }
} elseif ($role === 'user') {
    $sql = "SELECT SQL_CALC_FOUND_ROWS user_id as id, name, email as contact, 'user' as role FROM user WHERE 1=1";
    if ($q !== '') {
        $sql .= " AND (name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
    }
    $sql .= " ORDER BY name ASC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if ($q !== '') {
            $like = "%$q%";
            mysqli_stmt_bind_param($stmt, 'sssi', $like, $like, $like, $perPage);
            // note: binding offset as param can be problematic in some MySQL drivers; we'll append offset later
        }
        // execute with offset by appending in query if needed
        if ($q !== '') {
            mysqli_stmt_execute($stmt);
        } else {
            mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
            mysqli_stmt_execute($stmt);
        }
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) { $results[] = $row; }
        mysqli_stmt_close($stmt);
        $r = mysqli_query($conn, "SELECT FOUND_ROWS() AS cnt");
        $total = intval(mysqli_fetch_assoc($r)['cnt'] ?? 0);
    }
} else {
    // combined listing (simple union)
    $baseWhere = '';
    $params = [];
    if ($q !== '') {
        $like = "%$q%";
        $baseWhere = " WHERE driver_name LIKE ? OR phone_number LIKE ? OR name LIKE ? OR email LIKE ? OR phone_number LIKE ?";
    }
    // Use a union with LIMIT/OFFSET
    if ($q !== '') {
        $sql = "(SELECT driver_id as id, driver_name as name, phone_number as contact, 'driver' as role FROM driver WHERE driver_name LIKE ? OR phone_number LIKE ?) UNION ALL (SELECT user_id as id, name, email as contact, 'user' as role FROM user WHERE name LIKE ? OR email LIKE ? OR phone_number LIKE ?) ORDER BY name ASC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssiii', $like, $like, $like, $like, $like, $perPage, $offset);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($res)) { $results[] = $row; }
            mysqli_stmt_close($stmt);
        }
        // total fallback: separate counts
        $cntD = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM driver WHERE driver_name LIKE '" . mysqli_real_escape_string($conn, $like) . "' OR phone_number LIKE '" . mysqli_real_escape_string($conn, $like) . "'"))['c'] ?? 0;
        $cntU = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM user WHERE name LIKE '" . mysqli_real_escape_string($conn, $like) . "' OR email LIKE '" . mysqli_real_escape_string($conn, $like) . "' OR phone_number LIKE '" . mysqli_real_escape_string($conn, $like) . "'"))['c'] ?? 0;
        $total = $cntD + $cntU;
    } else {
        $sql = "(SELECT driver_id as id, driver_name as name, phone_number as contact, 'driver' as role FROM driver) UNION ALL (SELECT user_id as id, name, email as contact, 'user' as role FROM user) ORDER BY name ASC LIMIT ? OFFSET ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($res)) { $results[] = $row; }
            mysqli_stmt_close($stmt);
        }
        $cntD = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM driver"))['c'] ?? 0);
        $cntU = intval(mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM user"))['c'] ?? 0);
        $total = $cntD + $cntU;
    }
}

$pages = max(1, (int)ceil($total / $perPage));

include __DIR__ . "/../includes/admin_header.php";
?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Security Center</h1>
    <p class="admin-page-subtitle">Search, filter, and reset passwords for users and drivers.</p>
</div>

<div class="admin-card">
    <form method="GET" class="admin-form" style="display:flex;gap:0.5rem;align-items:center;">
        <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search name, email or phone" class="admin-form-control" style="flex:1;">
        <select name="role" class="admin-form-control">
            <option value="all" <?php echo $role === 'all' ? 'selected' : ''; ?>>All</option>
            <option value="driver" <?php echo $role === 'driver' ? 'selected' : ''; ?>>Drivers</option>
            <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>Users</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-primary">Search</button>
        <a href="/bus/admin/security.php" class="admin-btn admin-btn-secondary">Reset</a>
    </form>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h2 class="admin-card-title">Results (<?php echo $total; ?>)</h2></div>
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)): ?>
                    <?php foreach ($results as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(ucfirst($r['role'])); ?></td>
                            <td><?php echo htmlspecialchars($r['name']); ?></td>
                            <td><?php echo htmlspecialchars($r['contact'] ?? ''); ?></td>
                            <td>
                                <button class="admin-btn admin-btn-small" onclick="openPasswordModal('<?php echo htmlspecialchars($r['role']); ?>', '<?php echo (int)$r['id']; ?>', '<?php echo htmlspecialchars(addslashes($r['name'])); ?>')">Set/Reset</button>
                                <a href="<?php echo $r['role'] === 'driver' ? '/bus/admin/drivers/edit.php?id=' . (int)$r['id'] : '/bus/admin/users/edit.php?id=' . (int)$r['id']; ?>" class="admin-btn admin-btn-tertiary">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No results found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top:0.75rem; display:flex; justify-content:space-between; align-items:center;">
        <div>Page <?php echo $page; ?> of <?php echo $pages; ?></div>
        <div>
            <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page-1])); ?>" class="admin-btn admin-btn-secondary">Prev</a>
            <?php endif; ?>
            <?php if ($page < $pages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page+1])); ?>" class="admin-btn admin-btn-primary">Next</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div id="pwModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
    <div style="background:#fff; padding:1rem; width:420px; border-radius:6px;">
        <h3 id="modalTitle">Set Password</h3>
        <form method="POST" action="/bus/admin/security_action.php" id="pwForm">
            <input type="hidden" name="account" id="modalAccount">
            <div style="margin-bottom:0.5rem;">
                <label>New password (leave blank and check Generate to auto-generate)</label>
                <input type="password" name="new_password" id="modalPassword" class="admin-form-control">
            </div>
            <div style="margin-bottom:0.5rem;">
                <label>Confirm password</label>
                <input type="password" name="confirm_password" id="modalConfirm" class="admin-form-control">
            </div>
            <div style="margin-bottom:0.5rem; display:flex; gap:0.5rem; align-items:center;">
                <input type="checkbox" id="modalGenerate" name="generate_temp" value="1">
                <label for="modalGenerate">Generate temporary password</label>
            </div>
            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                <button type="button" onclick="closeModal()" class="admin-btn admin-btn-tertiary">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">Set/Reset Password</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../includes/admin_footer.php"; ?>

<script>
function openPasswordModal(role, id, name) {
    var acc = role + '|' + id;
    document.getElementById('modalAccount').value = acc;
    document.getElementById('modalTitle').textContent = 'Set/Reset password for ' + name;
    document.getElementById('modalPassword').value = '';
    document.getElementById('modalConfirm').value = '';
    document.getElementById('modalGenerate').checked = false;
    document.getElementById('pwModal').style.display = 'flex';
}
function closeModal() { document.getElementById('pwModal').style.display = 'none'; }
document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
</script>
