<?php
require_once '../includes/auth.php';
requireRole('developer');

$userName = getUserName();
$pdo = getDB();
$devId = getUserId();

// Total offers submitted
$stmt = $pdo->prepare("SELECT COUNT(*) FROM offers WHERE developer_id = ?");
$stmt->execute([$devId]);
$totalOffers = $stmt->fetchColumn();

// Accepted offers
$stmt = $pdo->prepare("SELECT COUNT(*) FROM offers WHERE developer_id = ? AND status = 'accepted'");
$stmt->execute([$devId]);
$acceptedOffers = $stmt->fetchColumn();

// Pending offers
$stmt = $pdo->prepare("SELECT COUNT(*) FROM offers WHERE developer_id = ? AND status = 'pending'");
$stmt->execute([$devId]);
$pendingOffers = $stmt->fetchColumn();

// Available open projects
$stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'open'");
$openProjects = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Developer Dashboard</title>
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
        <a href="dashboard.php" class="active-link">Dashboard</a>
        <a href="browse_projects.php">Browse Projects</a>
        <a href="my_offers.php">My Offers</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="client-hero section">
        <div class="hero-badge">Developer Portal</div>
        <h1>Hello, <span class="pink-text"><?= htmlspecialchars($userName) ?></span></h1>
        <p>Find projects, submit offers, and grow your freelance career.</p>
        <div class="hero-buttons" style="margin-top:28px;">
            <a href="browse_projects.php" class="primary-btn">Browse Projects</a>
            <a href="my_offers.php" class="secondary-btn">My Offers</a>
        </div>
    </section>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-projects.jpg" alt="Open Projects">
            </div>
            <span class="stat-label">Open Projects</span>
            <span class="stat-number"><?= $openProjects ?></span>
        </div>
        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-offers.jpg" alt="Total Offers">
            </div>
            <span class="stat-label">Offers Submitted</span>
            <span class="stat-number"><?= $totalOffers ?></span>
        </div>
        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-active.jpg" alt="Accepted">
            </div>
            <span class="stat-label">Accepted Offers</span>
            <span class="stat-number"><?= $acceptedOffers ?></span>
        </div>
        <div class="stat-card">
            <div class="stat-image-box">
                <img src="../assets/images/stat-completed.jpg" alt="Pending">
            </div>
            <span class="stat-label">Pending Offers</span>
            <span class="stat-number"><?= $pendingOffers ?></span>
        </div>
    </div>

    <section class="section">
        <div class="section-title">
            <span>What would you like to do?</span>
            <h2>Quick Actions</h2>
        </div>
        <div class="actions-grid">
            <a href="browse_projects.php" class="action-card">
                <div class="icon-box">🔍</div>
                <h3>Browse Projects</h3>
                <p>Explore available projects and find the right one for your skills.</p>
            </a>
            <a href="my_offers.php" class="action-card">
                <div class="icon-box">📨</div>
                <h3>My Offers</h3>
                <p>Track all offers you submitted and check their status.</p>
            </a>
            <a href="../contact.php" class="action-card">
                <div class="icon-box">💬</div>
                <h3>Contact Us</h3>
                <p>Need help? Reach out to the BridgeX team anytime.</p>
            </a>
        </div>
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
