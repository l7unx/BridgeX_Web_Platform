<?php
require_once '../includes/auth.php';
requireRole('developer');

$pdo = getDB();
$devId = getUserId();

$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$projectId) { header('Location: browse_projects.php'); exit; }

// Get project
$stmt = $pdo->prepare("SELECT p.*, u.name AS client_name FROM projects p JOIN users u ON p.client_id = u.id WHERE p.id = ? AND p.status = 'open'");
$stmt->execute([$projectId]);
$project = $stmt->fetch();
if (!$project) { header('Location: browse_projects.php'); exit; }

// Check already applied
$stmt = $pdo->prepare("SELECT id FROM offers WHERE project_id = ? AND developer_id = ?");
$stmt->execute([$projectId, $devId]);
$existingOffer = $stmt->fetch();

$success = '';
$error = '';

// Handle offer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$existingOffer) {
    $price = trim($_POST['price'] ?? '');
    $delivery = trim($_POST['delivery_time'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$price || !$delivery || !$message) {
        $error = 'All fields are required.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Please enter a valid price.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO offers (project_id, developer_id, price, delivery_time, message) VALUES (?,?,?,?,?)");
        $stmt->execute([$projectId, $devId, $price, $delivery, $message]);
        $success = 'Your offer has been submitted successfully!';
        $existingOffer = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Project Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/client.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="logo-area">
        <img src="../assets/images/logo.png" alt="BridgeX" class="logo-img">
        <span class="logo-text">Bridge<span style="color:var(--pink-main)">X</span></span>
    </div>
    <nav class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="browse_projects.php" class="active-link">Browse Projects</a>
        <a href="my_offers.php">My Offers</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">
        <a href="browse_projects.php" style="color:var(--pink-light);font-size:14px;display:inline-block;margin-bottom:22px;">← Back to Projects</a>

        <!-- Project Details Card -->
        <div class="form-card" style="margin-bottom:28px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:20px;">
                <div>
                    <span class="badge badge-open">Open</span>
                    <h2 style="font-size:26px;margin:10px 0 6px;"><?= htmlspecialchars($project['title']) ?></h2>
                    <p style="color:var(--text-muted);font-size:14px;">
                        Posted by <?= htmlspecialchars($project['client_name']) ?> &nbsp;|&nbsp;
                        <?= date('M d, Y', strtotime($project['created_at'])) ?>
                    </p>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:28px;font-weight:700;color:var(--pink-light)">$<?= number_format($project['budget'], 2) ?></div>
                    <div style="font-size:13px;color:var(--text-muted)">Budget</div>
                </div>
            </div>

            <div class="summary-grid" style="margin-bottom:20px;">
                <div class="summary-item">
                    <label>Project Type</label>
                    <span><?= htmlspecialchars($project['project_type']) ?></span>
                </div>
                <div class="summary-item">
                    <label>Duration</label>
                    <span><?= htmlspecialchars($project['duration']) ?></span>
                </div>
                <div class="summary-item full-width">
                    <label>Description</label>
                    <span><?= nl2br(htmlspecialchars($project['description'])) ?></span>
                </div>
                <?php if ($project['features']): ?>
                <div class="summary-item full-width">
                    <label>Required Features</label>
                    <span><?= nl2br(htmlspecialchars($project['features'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Offer Form -->
        <?php if ($existingOffer && !$success): ?>
            <div class="alert alert-success">✓ You have already submitted an offer for this project. <a href="my_offers.php">View My Offers</a></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success">✓ <?= $success ?> <a href="my_offers.php">View My Offers</a></div>
        <?php else: ?>
            <div class="form-card">
                <h3 class="form-section-title">Submit Your Offer</h3>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" id="offerForm" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Price ($) <span class="required">*</span></label>
                            <input type="number" name="price" id="price" placeholder="e.g. 500" min="1" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                            <span class="field-error" id="price-error"></span>
                        </div>
                        <div class="form-group">
                            <label>Delivery Time <span class="required">*</span></label>
                            <input type="text" name="delivery_time" id="delivery_time" placeholder="e.g. 2 weeks" value="<?= htmlspecialchars($_POST['delivery_time'] ?? '') ?>">
                            <span class="field-error" id="delivery-error"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cover Message <span class="required">*</span></label>
                        <textarea name="message" id="message" rows="5" placeholder="Describe your approach, experience, and why you're the best fit..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        <span class="field-error" id="message-error"></span>
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="primary-btn">Submit Offer</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </section>
</main>

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-brand">
            <div class="footer-logo">
                <img src="../assets/images/logo.png" alt="BridgeX">
                <span class="logo-text">Bridge<span style="color:var(--pink-main)">X</span></span>
            </div>
            <p>We connect clients with the best freelance developers.</p>
        </div>
        <div class="footer-links">
            <h4>Links</h4>
            <a href="browse_projects.php">Browse Projects</a>
            <a href="my_offers.php">My Offers</a>
            <a href="../contact.php">Contact Us</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

<script src="../assets/js/validation.js"></script>
</body>
</html>
