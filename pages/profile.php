<?php
include __DIR__ . "/../includes/auth.php";
require_login();
include __DIR__ . "/../includes/db.php";

$user_id = (int) ($_SESSION['user_id'] ?? 0);

// Fetch current user data
$user = null;

if ($user_id > 0) {
    if ($stmt = mysqli_prepare($conn, "SELECT name, email, phone_number FROM user WHERE user_id = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
    }
}

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '') {
        $message = "Name and email are required.";
        $messageClass = "alert-error";
    } else {
        $updateSql = "UPDATE user SET name = ?, email = ?, phone_number = ?" ;
        $params = [$name, $email, $phone];
        $types = "sss";

        if ($newPassword !== '' || $confirmPassword !== '') {
            if ($newPassword !== $confirmPassword) {
                $message = "New password and confirm password do not match.";
                $messageClass = "alert-error";
            } else {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateSql .= ", password = ?";
                $params[] = $hashed;
                $types .= "s";
            }
        }

        if ($message === "") {
            $updateSql .= " WHERE user_id = ?";
            $params[] = $user_id;
            $types .= "i";

            if ($stmt = mysqli_prepare($conn, $updateSql)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Profile updated successfully.";
                    $messageClass = "alert-success";

                    // Refresh in-memory data
                    $user['name'] = $name;
                    $user['email'] = $email;
                    $user['phone_number'] = $phone;
                    $_SESSION['user_name'] = $name;
                } else {
                    $message = "Failed to update profile. Please try again.";
                    $messageClass = "alert-error";
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = "Failed to prepare profile update.";
                $messageClass = "alert-error";
            }
        }
    }
}

include __DIR__ . "/../includes/header.php";
?>

<section class="dashboard container">
    <div class="dashboard-header">
        <div>
            <h2 class="dashboard-title"><i class="fa-solid fa-user-pen"></i> Edit profile</h2>
            <p class="dashboard-subtitle">
                Update your account details and password.
            </p>
        </div>
        <div class="dashboard-actions">
            <a href="<?php echo BASE_PATH; ?>/pages/dashboard.php" class="link-small">Back to dashboard</a>
        </div>
    </div>

    <section class="card">
        <div class="card-header">
            <div>
                <h3>Account information</h3>
                <p class="card-subtitle">These details are used for your bus monitoring account.</p>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="<?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($user): ?>
            <form method="post" class="auth-form" style="max-width: 480px;">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        required
                        value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        required
                        value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        class="form-control"
                        value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>"
                    >
                </div>

                <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e5e7eb;">

                <div class="form-group">
                    <label for="new_password">New password (optional)</label>
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        class="form-control"
                        placeholder="Leave blank to keep current password"
                    >
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm new password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-control"
                    >
                </div>

                <button type="submit" class="btn-primary" style="width: auto; margin-top: 0.5rem;">
                    Save changes
                </button>
            </form>
        <?php else: ?>
            <p class="empty-state">
                Unable to load your profile information right now.
            </p>
        <?php endif; ?>
    </section>
</section>

<?php include __DIR__ . "/../includes/footer.php"; ?>


