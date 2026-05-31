<?php
require_once '../includes/auth.php';
requireRole('admin');

$pdo = getDB();

// Delete message
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM messages WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: messages.php?deleted=1');
    exit;
}

$deleted = isset($_GET['deleted']);
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Contact Messages</title>
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
        <a href="users.php">Users</a>
        <a href="projects.php">Projects</a>
        <a href="offers.php">Offers</a>
        <a href="messages.php" class="active-link">Messages</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">
        <div class="section-title"><span>Admin Panel</span><h2>Contact Messages</h2></div>

        <?php if ($deleted): ?>
            <div class="alert alert-success">Message deleted successfully.</div>
        <?php endif; ?>

        <?php if (empty($messages)): ?>
            <div class="empty-state form-card">
                <div style="font-size:48px;">✉️</div>
                <h3>No Messages Yet</h3>
                <p style="color:var(--text-muted)">Messages submitted through the contact form will appear here.</p>
            </div>
        <?php else: ?>
            <div class="offers-grid">
                <?php foreach ($messages as $m): ?>
                    <div class="offer-card">
                        <div class="offer-card-header">
                            <div class="dev-avatar"><?= strtoupper(substr($m['name'],0,1)) ?></div>
                            <div>
                                <h4 style="margin:0 0 3px;"><?= htmlspecialchars($m['name']) ?></h4>
                                <p style="font-size:13px;color:var(--pink-light)"><?= htmlspecialchars($m['email']) ?></p>
                            </div>
                            <span style="margin-left:auto;font-size:12px;color:var(--text-muted)"><?= date('M d, Y', strtotime($m['created_at'])) ?></span>
                        </div>
                        <div class="offer-message"><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                        <div style="display:flex;gap:12px;align-items:center;">
                            <a href="mailto:<?= htmlspecialchars($m['email']) ?>" class="btn-accept btn-sm">Reply via Email</a>
                            <a href="?delete=<?= $m['id'] ?>" class="btn-reject btn-sm" onclick="return confirm('Delete this message?')">Delete</a>
                        </div>
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
            <a href="users.php">Users</a>
            <a href="projects.php">Projects</a>
            <a href="messages.php">Messages</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

</body>
</html>
