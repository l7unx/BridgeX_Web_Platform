<?php
require_once '../includes/auth.php';
requireRole('admin');

$pdo = getDB();

// Filter
$status = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');

$sql = "SELECT o.*, u.name AS dev_name, p.title AS project_title, p.budget AS project_budget
        FROM offers o
        JOIN users u ON o.developer_id=u.id
        JOIN projects p ON o.project_id=p.id
        WHERE 1";
$params = [];
if ($status) { $sql .= " AND o.status=?"; $params[] = $status; }
if ($search) { $sql .= " AND (u.name LIKE ? OR p.title LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$offers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Manage Offers</title>
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
        <a href="offers.php" class="active-link">Offers</a>
        <a href="messages.php">Messages</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">
        <div class="section-title"><span>Admin Panel</span><h2>Manage Offers</h2></div>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by developer or project..." value="<?= htmlspecialchars($search) ?>" class="filter-input">
            <select name="status" class="filter-select">
                <option value="">All Statuses</option>
                <option value="pending" <?= $status==='pending'?'selected':'' ?>>Pending</option>
                <option value="accepted" <?= $status==='accepted'?'selected':'' ?>>Accepted</option>
                <option value="rejected" <?= $status==='rejected'?'selected':'' ?>>Rejected</option>
            </select>
            <button type="submit" class="primary-btn btn-sm">Filter</button>
            <?php if ($search || $status): ?><a href="offers.php" class="secondary-btn btn-sm">Clear</a><?php endif; ?>
        </form>

        <div class="form-card">
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Developer</th><th>Project</th><th>Price</th><th>Delivery</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php if (empty($offers)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:30px;">No offers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($offers as $o):
                        $bc = match($o['status']) { 'accepted'=>'badge-accepted','rejected'=>'badge-rejected', default=>'badge-pending' };
                    ?>
                    <tr>
                        <td><?= $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['dev_name']) ?></td>
                        <td style="color:var(--text-muted)"><?= htmlspecialchars(substr($o['project_title'],0,30)) ?>...</td>
                        <td style="color:var(--pink-light)">$<?= number_format($o['price'],2) ?></td>
                        <td style="color:var(--text-muted)"><?= htmlspecialchars($o['delivery_time']) ?></td>
                        <td><span class="badge <?= $bc ?>"><?= ucfirst($o['status']) ?></span></td>
                        <td style="color:var(--text-muted)"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
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
            <a href="users.php">Users</a>
            <a href="projects.php">Projects</a>
            <a href="messages.php">Messages</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

</body>
</html>
