<?php
require_once '../includes/auth.php';
requireRole('admin');

$pdo = getDB();

$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalClients  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='client'")->fetchColumn();
$totalDevs     = $pdo->query("SELECT COUNT(*) FROM users WHERE role='developer'")->fetchColumn();
$totalProjects = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$openProjects  = $pdo->query("SELECT COUNT(*) FROM projects WHERE status='open'")->fetchColumn();
$totalOffers   = $pdo->query("SELECT COUNT(*) FROM offers")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();

// Recent activity
$recentProjects = $pdo->query("SELECT p.title, u.name AS client, p.created_at FROM projects p JOIN users u ON p.client_id=u.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
$recentOffers   = $pdo->query("SELECT o.price, o.status, u.name AS dev, p.title FROM offers o JOIN users u ON o.developer_id=u.id JOIN projects p ON o.project_id=p.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Admin Dashboard</title>
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
        <a href="users.php">Users</a>
        <a href="projects.php">Projects</a>
        <a href="offers.php">Offers</a>
        <a href="messages.php">Messages</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="client-hero section">
        <div class="hero-badge">Admin Control Panel</div>
        <h1>Welcome, <span class="pink-text"><?= htmlspecialchars(getUserName()) ?></span></h1>
        <p>Manage users, projects, offers, and platform activity.</p>
    </section>

    <!-- Stats -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon">👥</div>
            <div class="admin-stat-info">
                <span class="stat-number"><?= $totalUsers ?></span>
                <span class="stat-label">Total Users</span>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">🧑‍💼</div>
            <div class="admin-stat-info">
                <span class="stat-number"><?= $totalClients ?></span>
                <span class="stat-label">Clients</span>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">💻</div>
            <div class="admin-stat-info">
                <span class="stat-number"><?= $totalDevs ?></span>
                <span class="stat-label">Developers</span>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">📁</div>
            <div class="admin-stat-info">
                <span class="stat-number"><?= $totalProjects ?></span>
                <span class="stat-label">Projects</span>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">🟢</div>
            <div class="admin-stat-info">
                <span class="stat-number"><?= $openProjects ?></span>
                <span class="stat-label">Open Projects</span>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">📨</div>
            <div class="admin-stat-info">
                <span class="stat-number"><?= $totalOffers ?></span>
                <span class="stat-label">Total Offers</span>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">✉️</div>
            <div class="admin-stat-info">
                <span class="stat-number"><?= $totalMessages ?></span>
                <span class="stat-label">Messages</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <section class="section">
        <div class="section-title"><span>Admin Tools</span><h2>Quick Actions</h2></div>
        <div class="actions-grid">
            <a href="users.php" class="action-card">
                <div class="icon-box">👥</div>
                <h3>Manage Users</h3>
                <p>View, search, and delete platform users.</p>
            </a>
            <a href="projects.php" class="action-card">
                <div class="icon-box">📁</div>
                <h3>Manage Projects</h3>
                <p>View all projects and update their status.</p>
            </a>
            <a href="offers.php" class="action-card">
                <div class="icon-box">📨</div>
                <h3>Manage Offers</h3>
                <p>Review all submitted developer offers.</p>
            </a>
            <a href="messages.php" class="action-card">
                <div class="icon-box">✉️</div>
                <h3>Contact Messages</h3>
                <p>Read messages submitted through the contact form.</p>
            </a>
        </div>
    </section>

    <!-- Recent Activity -->
    <div class="form-row section">
        <div class="form-card">
            <h3 class="form-section-title">Recent Projects</h3>
            <table class="admin-table">
                <thead><tr><th>Title</th><th>Client</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($recentProjects as $rp): ?>
                    <tr>
                        <td><?= htmlspecialchars($rp['title']) ?></td>
                        <td><?= htmlspecialchars($rp['client']) ?></td>
                        <td><?= date('M d, Y', strtotime($rp['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="form-card">
            <h3 class="form-section-title">Recent Offers</h3>
            <table class="admin-table">
                <thead><tr><th>Developer</th><th>Project</th><th>Price</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentOffers as $ro):
                    $bc = $ro['status']==='accepted' ? 'badge-accepted' : ($ro['status']==='rejected' ? 'badge-rejected' : 'badge-pending');
                ?>
                    <tr>
                        <td><?= htmlspecialchars($ro['dev']) ?></td>
                        <td><?= htmlspecialchars(substr($ro['title'],0,25)) ?>...</td>
                        <td>$<?= number_format($ro['price'],2) ?></td>
                        <td><span class="badge <?= $bc ?>"><?= ucfirst($ro['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
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
            <a href="users.php">Users</a>
            <a href="projects.php">Projects</a>
            <a href="messages.php">Messages</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

</body>
</html>
