</main>
<?php
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/config.php';
}
?>
<footer class="site-footer">
    <div class="footer-inner container">
        <div class="footer-brand">
            <div>
                <h3 class="footer-title">IUBAT Bus Monitoring</h3>
                <p class="footer-tagline">Real-time campus bus tracking for students and commuters.</p>
            </div>
        </div>

        <div class="footer-columns">
            <div class="footer-column">
                <h4>Product</h4>
                <ul>
                    <li><a href="<?php echo BASE_PATH; ?>/index.php">Overview</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/pages/login.php">Login</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/pages/register.php">Register</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Routes</h4>
                <ul>
                    <li><a href="<?php echo BASE_PATH; ?>/pages/dashboard.php">User dashboard</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/pages/routes.php">Available routes</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Contact</h4>
                <ul>
                    <li><a href="mailto:info@localbusmonitoring.test">info@localbusmonitoring.test</a></li>
                    <li><a href="tel:+8801700000000">+88 01700-000000</a></li>
                    <li><span>Dhaka, Bangladesh</span></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; <?php echo date('Y'); ?> IUBAT Bus Monitoring. All rights reserved.</p>
            <div class="footer-social">
                <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">Fb</a>
                <a href="https://www.twitter.com" target="_blank" rel="noopener noreferrer">X</a>
                <a href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer">In</a>
            </div>
        </div>
    </div>
</footer>
</body>
</html>


