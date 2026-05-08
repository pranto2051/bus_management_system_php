<?php
session_start();
include __DIR__ . "/../includes/db.php";

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $loginId = trim($_POST['email'] ?? ''); // can be user email, driver name or phone
    $password = $_POST['password'] ?? '';

    // Try user (email) first
    $userFound = false;
    if ($stmt = mysqli_prepare($conn, "SELECT user_id, name, password FROM `user` WHERE email = ? LIMIT 1")) {
        mysqli_stmt_bind_param($stmt, 's', $loginId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && mysqli_num_rows($res) === 1) {
            $row = mysqli_fetch_assoc($res);
            $userFound = true;
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_name'] = $row['name'];
                $message = "Login successful! Welcome, " . htmlspecialchars($row['name']);
                $messageClass = "alert-success";
                header("Location: " . BASE_PATH . "/pages/dashboard.php");
                exit();
            } else {
                $message = "Incorrect password";
                $messageClass = "alert-error";
            }
        }
        mysqli_stmt_close($stmt);
    }

    // If not found as user, try driver (match on driver_name OR phone_number)
    if (!$userFound) {
        // Normalize digits-only phone input for matching
        $loginDigits = preg_replace('/\D+/', '', $loginId);
        if ($stmt = mysqli_prepare($conn, "SELECT driver_id, driver_name, phone_number, password FROM `driver` WHERE driver_name = ? OR REPLACE(REPLACE(REPLACE(phone_number, '+', ''), ' ', ''), '-', '') = ? LIMIT 1")) {
            // Bind driver_name and normalized phone digits
            mysqli_stmt_bind_param($stmt, 'ss', $loginId, $loginDigits);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res && mysqli_num_rows($res) === 1) {
                $row = mysqli_fetch_assoc($res);
                if (password_verify($password, $row['password'])) {
                    $_SESSION['driver_id'] = $row['driver_id'];
                    $_SESSION['driver_name'] = $row['driver_name'];
                    $message = "Login successful! Welcome, " . htmlspecialchars($row['driver_name']);
                    $messageClass = "alert-success";
                    // Redirect drivers to their dashboard
                    header("Location: " . BASE_PATH . "/pages/driver_dashboard.php");
                    exit();
                } else {
                    $message = "Incorrect password";
                    $messageClass = "alert-error";
                }
            } else {
                $message = "Account not found";
                $messageClass = "alert-error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
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
                            “Track routes, schedules, and passenger flow from one clear dashboard.”
                        </p>
                        <p class="auth-quote-meta">
                            Built for <span>smarter transit operations</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="auth-panel">
                <div class="auth-panel-inner">
                    <div class="auth-heading">
                        <h2>Welcome Back</h2>
                        <p>Sign in with your email and password to continue.</p>
                    </div>

                    <?php if (!empty($message)) : ?>
                        <div class="<?php echo $messageClass; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="email">Email or driver name</label>
                            <input
                                type="text"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="you@example.com or driver name"
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
                            <div style="margin-top:8px;font-size:0.9rem;display:flex;align-items:center;gap:8px;">
                                <input type="checkbox" id="show_password_toggle">
                                <label for="show_password_toggle" style="cursor:pointer;">Show password</label>
                            </div>
                        </div>

                        <div class="auth-row">
                            <label class="switch-label">
                                <span>Remember me</span>
                                <span class="switch">
                                    <span class="switch-handle"></span>
                                </span>
                            </label>
                            <a href="#" class="link-small">Forgot password?</a>
                        </div>

                        <button type="submit" name="login" class="btn-primary">
                            Log In
                        </button>
                    </form>

                    <div class="auth-meta">
                        <span>Don't have an account?</span>
                        <a href="<?php echo BASE_PATH; ?>/pages/register.php">Sign up</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

<script>
// Toggle password visibility on the login page
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('show_password_toggle');
    var pwd = document.getElementById('password');
    if (toggle && pwd) {
        toggle.addEventListener('change', function () {
            pwd.type = this.checked ? 'text' : 'password';
        });
    }
});
</script>


