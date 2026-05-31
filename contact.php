<?php
require_once 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($message) < 10) {
        $error = 'Message must be at least 10 characters.';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?,?,?)");
        $stmt->execute([$name, $email, $message]);
        $success = 'Your message has been sent! We will get back to you soon.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Contact Us</title>
    <link rel="stylesheet" href="/bridgex/assets/css/style.css">
    <link rel="stylesheet" href="/bridgex/assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="logo-area">
        <img src="/bridgex/assets/images/logo.png" alt="BridgeX Logo" class="logo-img">
        <span class="logo-text">Bridge<span style="color:var(--pink-main)">X</span></span>
    </div>
    <nav class="navbar">
        <a href="/bridgex/index.php">Home</a>
        <a href="/bridgex/about.php">About</a>
        <a href="/bridgex/contact.php" class="active-link">Contact</a>
        <a href="/bridgex/login.php" class="login-link">Login</a>
        <a href="/bridgex/register.php" class="nav-btn">Start Now</a>
    </nav>
</header>

<main>
    <section class="section" style="max-width:680px;margin:0 auto;">
        <div class="section-title" style="text-align:center;">
            <span>We'd love to hear from you</span>
            <h2>Contact Us</h2>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form id="contactForm" method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" placeholder="Your name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    <span class="field-error" id="name-error"></span>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <span class="field-error" id="email-error"></span>
                </div>

                <div class="form-group">
                    <label for="message">Message <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="6" placeholder="Write your message here..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    <span class="field-error" id="message-error"></span>
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="primary-btn">Send Message</button>
                </div>
            </form>
        </div>

        <!-- Contact Info -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:28px;">
            <div class="form-card" style="text-align:center;padding:24px 16px;">
                <div style="font-size:28px;margin-bottom:10px;">📧</div>
                <h4 style="font-size:14px;margin-bottom:6px;">Email</h4>
                <p style="color:var(--text-muted);font-size:13px;">support@bridgex.com</p>
            </div>
            <div class="form-card" style="text-align:center;padding:24px 16px;">
                <div style="font-size:28px;margin-bottom:10px;">📍</div>
                <h4 style="font-size:14px;margin-bottom:6px;">Location</h4>
                <p style="color:var(--text-muted);font-size:13px;">Riyadh, Saudi Arabia</p>
            </div>
            <div class="form-card" style="text-align:center;padding:24px 16px;">
                <div style="font-size:28px;margin-bottom:10px;">⏰</div>
                <h4 style="font-size:14px;margin-bottom:6px;">Response Time</h4>
                <p style="color:var(--text-muted);font-size:13px;">Within 24 hours</p>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

<script src="/bridgex/assets/js/validation.js"></script>
</body>
</html>
