<?php
include __DIR__ . "/../includes/db.php";

$message = "";
$messageClass = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    $query = "INSERT INTO `user` (name, email, phone_number, password)
              VALUES ('$name', '$email', '$phone', '$password')";

    if (mysqli_query($conn, $query)) {
        $message = "Registration successful!";
        $messageClass = "alert-success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $messageClass = "alert-error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="/bus/assets/css/style.css">
</head>
<body class="auth-body register-page">
<section class="auth-page">
    <div class="auth-layout-card">
        <div class="auth-visual">
            <div class="auth-visual-overlay">
                <div class="auth-brand">
                    <span class="auth-logo-circle"></span>
                </div>
                <div class="auth-quote">
                    <p class="auth-quote-text">
                        “A cleaner way to follow every bus, stop, and schedule in real time.”
                    </p>
                    <p class="auth-quote-meta">
                        Join the journey with <span>one live transit view</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="auth-panel">
            <div class="auth-panel-inner">
                <div class="auth-heading">
                    <h2>Create your account</h2>
                    <span class="auth-brand-name">IUBAT Bus Monitoring</span>
                                    <p>Sign up to start using the IUBAT Bus Monitoring system.</p>
                </div>

                <?php if (!empty($message)) : ?>
                    <div class="<?php echo $messageClass; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            required
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
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="form-control"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                        >
                    </div>

                    <button type="submit" name="register" class="btn-primary">
                        Register
                    </button>
                </form>

                <div class="auth-meta">
                    <span>Already have an account?</span>
                    <a href="/bus/pages/login.php">Log in</a>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
