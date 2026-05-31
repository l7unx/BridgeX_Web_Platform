<?php
require_once '../includes/auth.php';
requireRole('developer');

$pdo = getDB();
$devId = getUserId();

$stmt = $pdo->prepare("
    SELECT o.*, p.title AS project_title, p.budget AS project_budget, p.project_type, p.status AS project_status
    FROM offers o
    JOIN projects p ON o.project_id = p.id
    WHERE o.developer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$devId]);
$offers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — My Offers</title>
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
        <a href="browse_projects.php">Browse Projects</a>
        <a href="my_offers.php" class="active-link">My Offers</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">
        <div class="section-title">
            <span>Track your submissions</span>
            <h2>My Offers</h2>
        </div>

        <?php if (empty($offers)): ?>
            <div class="empty-state form-card">
                <div style="font-size:48px;">📨</div>
                <h3>No Offers Yet</h3>
                <p style="color:var(--text-muted)">Browse available projects and submit your first offer.</p>
                <a href="browse_projects.php" class="primary-btn" style="margin-top:20px;display:inline-block;">Browse Projects</a>
            </div>
        <?php else: ?>
            <div class="offers-grid">
                <?php foreach ($offers as $o):
                    $badgeClass = 'badge-pending';
                    if ($o['status'] === 'accepted') $badgeClass = 'badge-accepted';
                    if ($o['status'] === 'rejected') $badgeClass = 'badge-rejected';
                ?>
                    <div class="offer-card">
                        <div class="offer-card-header">
                            <div class="dev-avatar"><?= strtoupper(substr(getUserName(), 0, 1)) ?></div>
                            <div>
                                <h4 style="margin:0 0 4px;"><?= htmlspecialchars($o['project_title']) ?></h4>
                                <p style="font-size:13px;color:var(--text-muted);">
                                    Type: <?= htmlspecialchars($o['project_type']) ?> &nbsp;|&nbsp;
                                    Submitted: <?= date('M d, Y', strtotime($o['created_at'])) ?>
                                </p>
                            </div>
                            <span class="badge <?= $badgeClass ?>" style="margin-left:auto;"><?= ucfirst($o['status']) ?></span>
                        </div>
                        <div class="offer-details-row">
                            <div class="offer-detail">
                                <strong>$<?= number_format($o['price'], 2) ?></strong><br>Your Price
                            </div>
                            <div class="offer-detail">
                                <strong><?= htmlspecialchars($o['delivery_time']) ?></strong><br>Delivery
                            </div>
                            <div class="offer-detail">
                                <strong>$<?= number_format($o['project_budget'], 2) ?></strong><br>Project Budget
                            </div>
                        </div>
                        <div class="offer-message"><?= nl2br(htmlspecialchars($o['message'])) ?></div>

                        <?php if ($o['status'] === 'accepted'): ?>
                            <div style="color:#7ee8e2;font-size:13px;font-weight:600;">🎉 Congratulations! Your offer was accepted.</div>
                        <?php elseif ($o['status'] === 'rejected'): ?>
                            <div style="color:#ff9090;font-size:13px;">Your offer was not selected for this project.</div>
                        <?php else: ?>
                            <div style="color:var(--text-muted);font-size:13px;">⏳ Waiting for the client to review your offer.</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
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

</body>
</html>
