<?php
require_once '../includes/auth.php';
requireRole('developer');

$pdo = getDB();
$devId = getUserId();

// Filter
$typeFilter = isset($_GET['type']) ? trim($_GET['type']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT p.*, u.name AS client_name,
        (SELECT COUNT(*) FROM offers WHERE project_id = p.id) AS offer_count,
        (SELECT COUNT(*) FROM offers WHERE project_id = p.id AND developer_id = ?) AS already_applied
        FROM projects p
        JOIN users u ON p.client_id = u.id
        WHERE p.status = 'open'";
$params = [$devId];

if ($typeFilter) {
    $sql .= " AND p.project_type = ?";
    $params[] = $typeFilter;
}
if ($search) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Get distinct types
$types = $pdo->query("SELECT DISTINCT project_type FROM projects WHERE status='open' ORDER BY project_type")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Browse Projects</title>
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
        <div class="section-title">
            <span>Find Your Next Project</span>
            <h2>Available Projects</h2>
        </div>

        <!-- Filter Bar -->
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search projects..." value="<?= htmlspecialchars($search) ?>" class="filter-input">
            <select name="type" class="filter-select">
                <option value="">All Types</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>" <?= $typeFilter === $t ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="primary-btn btn-sm">Filter</button>
            <?php if ($typeFilter || $search): ?>
                <a href="browse_projects.php" class="secondary-btn btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <?php if (empty($projects)): ?>
            <div class="empty-state form-card">
                <div style="font-size:48px;">🔍</div>
                <h3>No Projects Found</h3>
                <p style="color:var(--text-muted)">Try adjusting your search or check back later.</p>
            </div>
        <?php else: ?>
            <div class="projects-list">
                <?php foreach ($projects as $p): ?>
                    <div class="project-row form-card">
                        <div class="project-row-header">
                            <div>
                                <span class="badge badge-open">Open</span>
                                <h3 class="project-title"><?= htmlspecialchars($p['title']) ?></h3>
                                <p class="project-meta">
                                    Type: <?= htmlspecialchars($p['project_type']) ?> &nbsp;|&nbsp;
                                    Budget: <strong style="color:var(--pink-light)">$<?= number_format($p['budget'], 2) ?></strong> &nbsp;|&nbsp;
                                    Duration: <?= htmlspecialchars($p['duration']) ?> &nbsp;|&nbsp;
                                    Posted by: <?= htmlspecialchars($p['client_name']) ?> &nbsp;|&nbsp;
                                    <?= $p['offer_count'] ?> offer(s)
                                </p>
                            </div>
                            <div class="project-row-actions">
                                <?php if ($p['already_applied']): ?>
                                    <span class="badge badge-accepted">✓ Applied</span>
                                <?php else: ?>
                                    <a href="project_details.php?id=<?= $p['id'] ?>" class="primary-btn btn-sm">View & Apply</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="project-desc"><?= nl2br(htmlspecialchars(substr($p['description'], 0, 200))) ?>...</p>
                        <a href="project_details.php?id=<?= $p['id'] ?>" style="color:var(--pink-light);font-size:13px;">View Full Details →</a>
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
