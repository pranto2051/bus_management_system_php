<?php
session_start();
include __DIR__ . "/../includes/db.php";
include __DIR__ . "/../includes/admin_auth.php";

redirect_if_admin_logged_in();

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT admin_id, username, admin_password FROM admin WHERE username = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['admin_password'])) {
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['admin_username'] = $row['username'];

                header("Location: /bus/admin/dashboard.php");
                exit();
            } else {
                $message = "Incorrect password";
                $messageClass = "alert-error";
            }
        } else {
            $message = "Admin not found";
            $messageClass = "alert-error";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - IUBAT Bus Monitoring</title>
    <link rel="stylesheet" href="/bus/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">
    <section class="auth-page">
        <div class="auth-layout-card">
            <div class="auth-visual">
                <div class="auth-visual-overlay">
                    <div class="auth-brand">
                        <span class="auth-logo-circle"></span>
                        <span class="auth-brand-name">IUBAT Bus Monitoring</span>
                    </div>
                    <div class="auth-quote">
                        <p class="auth-quote-text">
                            "Monitor routes, manage schedules, and keep every operation in sync."
                        </p>
                        <p class="auth-quote-meta">
                            Admin access <span>only</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="auth-panel">
                <div class="auth-panel-inner">
                    <div class="auth-heading">
                        <h2>Admin Login</h2>
                        <p>Sign in with your admin credentials to access the panel.</p>
                    </div>

                    <?php if (!empty($message)) : ?>
                        <div class="<?php echo $messageClass; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control"
                                placeholder="Enter admin username"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter your password"
                                required
                            >
                        </div>

                        <button type="submit" name="login" class="btn-primary">
                            <i class="fa-solid fa-right-to-bracket"></i> Log In
                        </button>
                    </form>

                    <div class="auth-meta">
                        <span>Regular user?</span>
                        <a href="/bus/pages/login.php">User Login</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

