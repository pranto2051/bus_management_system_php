<?php include __DIR__ . "/includes/header.php"; ?>

<section class="landing-section">
    <div class="landing-wrapper">
        <div class="landing-hero">
            <div class="landing-hero-text">
                <p class="landing-kicker">IUBAT Bus Monitoring</p>
                <h2>Smart campus bus tracking, made simple.</h2>
                <p class="landing-subtitle">
                    Check live map snapshots, upcoming departures, and your favourite routes –
                    all from one clean dashboard built for students.
                </p>

                <div class="landing-actions">
                    <a href="/bus/pages/login.php" class="btn-primary landing-cta">
                        <i class="fa-solid fa-right-to-bracket"></i> Log in
                    </a>
                    <a href="/bus/pages/register.php" class="btn-secondary landing-cta">
                        <i class="fa-regular fa-user"></i> Create an account
                    </a>
                </div>

                <!-- Quick driver login (compact) -->
                <div class="driver-login-quick" style="margin-top:18px;">
                    <form method="POST" action="<?php echo BASE_PATH; ?>/pages/login.php" class="driver-login-form" style="display:flex;gap:8px;align-items:center;">
                        <input type="text" name="email" placeholder="Driver name or phone" class="form-control" style="padding:8px;border-radius:6px;border:1px solid var(--border);width:220px;" required>
                        <input id="driver_quick_password" type="password" name="password" placeholder="Password" class="form-control" style="padding:8px;border-radius:6px;border:1px solid var(--border);width:160px;" required>
                        <button type="submit" name="login" class="btn-primary" style="padding:8px 12px;border-radius:6px;">Driver Login</button>
                    </form>
                    <div style="margin-top:6px;display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" id="driver_show_password">
                        <label for="driver_show_password" style="cursor:pointer;color:var(--text-muted);font-size:0.9rem;">Show password</label>
                    </div>
                    <p style="margin:8px 0 0;color:var(--text-muted);font-size:0.9rem;">Use your driver name or phone number and password to sign in.</p>
                </div>

                <div class="landing-highlights">
                    <div class="highlight-item">
                        <span class="highlight-icon"><i class="fa-solid fa-bus"></i></span>
                        <div>
                            <h3>Live-like updates</h3>
                            <p>Schedule-based “live” statuses so you know when to leave home.</p>
                        </div>
                    </div>
                    <div class="highlight-item">
                        <span class="highlight-icon"><i class="fa-solid fa-route"></i></span>
                        <div>
                            <h3>All routes in one view</h3>
                            <p>Explore every campus route, stop, and schedule at a glance.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="landing-hero-visual">
                <img
                    src="/bus/assets/img/bus_pink.png"
                    alt="Pink school bus illustration"
                    class="landing-bus-image"
                >
            </div>
        </div>

        <!-- Features section -->
        <section class="landing-features">
            <h3 class="landing-section-title">Why use IUBAT Bus Monitoring?</h3>
            <p class="landing-section-subtitle">
                A lightweight tool designed for students who rely on campus buses every day.
            </p>
            <div class="feature-grid">
                <article class="feature-card">
                    <span class="feature-icon">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </span>
                    <h4>Live map snapshot</h4>
                    <p>See the latest known position of every bus so you are never guessing at the stop.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-icon">
                        <i class="fa-solid fa-table-list"></i>
                    </span>
                    <h4>Full route browser</h4>
                    <p>Browse routes, stops and schedules in a clean table view – no PDF timetables.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-icon">
                        <i class="fa-solid fa-heart"></i>
                    </span>
                    <h4>Favourite lines</h4>
                    <p>Star your most used routes so they always appear at the top of your dashboard.</p>
                </article>
                <article class="feature-card">
                    <span class="feature-icon">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <h4>Schedule-based alerts</h4>
                    <p>See friendly labels like “On route” or “Starts in 10 min” based on the day’s schedule.</p>
                </article>
            </div>
        </section>

        <!-- Buses section -->
        <?php
        include __DIR__ . "/includes/db.php";
        
        // Fetch buses with route and schedule info
        $buses = [];
        $buses_sql = "
            SELECT 
                b.bus_id,
                b.bus_num,
                b.capacity,
                r.route_name,
                s.departure_time,
                s.arrival_time,
                d.driver_name
            FROM bus b
            JOIN route r ON r.route_id = b.route_id
            LEFT JOIN schedule s ON s.schedule_id = b.schedule_id
            LEFT JOIN driver d ON d.driver_id = s.driver_id
            ORDER BY b.bus_num ASC
            LIMIT 12
        ";
        
        if ($result = mysqli_query($conn, $buses_sql)) {
            while ($row = mysqli_fetch_assoc($result)) {
                $buses[] = $row;
            }
            mysqli_free_result($result);
        }
        ?>
        
        <section class="landing-buses">
            <h3 class="landing-section-title">Our fleet</h3>
            <p class="landing-section-subtitle">
                Browse our active buses and their routes across campus.
            </p>
            <?php if (!empty($buses)): ?>
                <div class="buses-grid">
                    <?php foreach ($buses as $bus): ?>
                        <article class="bus-card">
                            <div class="bus-card-header">
                                <span class="bus-number"><?php echo htmlspecialchars($bus['bus_num']); ?></span>
                                <span class="bus-capacity">
                                    <i class="fa-solid fa-users"></i> <?php echo htmlspecialchars($bus['capacity']); ?> seats
                                </span>
                            </div>
                            <div class="bus-card-body">
                                <h4 class="bus-route">
                                    <i class="fa-solid fa-route"></i> <?php echo htmlspecialchars($bus['route_name']); ?>
                                </h4>
                                <?php if ($bus['departure_time'] && $bus['arrival_time']): ?>
                                    <div class="bus-schedule">
                                        <span class="schedule-time">
                                            <i class="fa-solid fa-clock"></i> 
                                            <?php echo date('H:i', strtotime($bus['departure_time'])); ?> - 
                                            <?php echo date('H:i', strtotime($bus['arrival_time'])); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($bus['driver_name']): ?>
                                    <div class="bus-driver">
                                        <i class="fa-solid fa-id-card"></i> <?php echo htmlspecialchars($bus['driver_name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-state">No buses available at the moment.</p>
            <?php endif; ?>
        </section>

        <!-- How it works section -->
        <section class="landing-steps">
            <h3 class="landing-section-title">How it works</h3>
            <div class="steps-grid">
                <div class="step-item">
                    <span class="step-badge">01</span>
                    <div>
                        <h4>Create your account</h4>
                        <p>Sign up with your campus email and log in to your personal dashboard.</p>
                    </div>
                </div>
                <div class="step-item">
                    <span class="step-badge">02</span>
                    <div>
                        <h4>Pick routes & view map</h4>
                        <p>Browse available routes, mark favourites, and open the live bus map.</p>
                    </div>
                </div>
                <div class="step-item">
                    <span class="step-badge">03</span>
                    <div>
                        <h4>Catch the bus on time</h4>
                        <p>Use the schedule-based status to leave home at the perfect moment.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ section -->
        <section class="landing-faq">
            <h3 class="landing-section-title">Frequently asked questions</h3>
            <div class="faq-grid">
                <div class="faq-item">
                    <h4>Is this using real GPS data?</h4>
                    <p>Right now the system simulates live information from the official bus schedule. GPS can be added later without changing the UI.</p>
                </div>
                <div class="faq-item">
                    <h4>Do I need an account to see routes?</h4>
                    <p>Yes, you sign in once and then you can access routes, favourites and the map from your dashboard.</p>
                </div>
                <div class="faq-item">
                    <h4>Can I use this on mobile?</h4>
                    <p>The interface is responsive, so you can quickly check buses from your phone’s browser.</p>
                </div>
                <div class="faq-item">
                    <h4>Is there an admin panel?</h4>
                    <p>This landing focuses on the student view. Admin tools for managing routes and schedules can be added behind a separate login.</p>
                </div>
            </div>
        </section>

        <div class="landing-strip">
            <div class="landing-strip-inner">
                <div class="landing-strip-item">
                    <h4>Lorem ipsum</h4>
                    <p>Dolor sit amet</p>
                </div>
                <div class="landing-strip-item">
                    <h4>Dolor sit amet</h4>
                    <p>Lorem ipsum</p>
                </div>
                <div class="landing-strip-item">
                    <h4>Lorem ipsum</h4>
                    <p>Dolor sit amet</p>
                </div>
                <div class="landing-strip-arrows">
                    <button type="button" class="strip-arrow-btn" aria-label="Previous">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" class="strip-arrow-btn" aria-label="Next">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . "/includes/footer.php"; ?>

<script>
// Toggle password visibility for quick driver login on homepage
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('driver_show_password');
    var pwd = document.getElementById('driver_quick_password');
    if (toggle && pwd) {
        toggle.addEventListener('change', function () {
            pwd.type = this.checked ? 'text' : 'password';
        });
    }
});
</script>
