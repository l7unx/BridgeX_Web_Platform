<?php
require_once '../includes/auth.php';
requireRole('admin');

$pdo = getDB();
$msg = '';

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'], $_POST['status'])) {
    $allowed = ['open','in_progress','completed','cancelled'];
    if (in_array($_POST['status'], $allowed)) {
        $pdo->prepare("UPDATE projects SET status=? WHERE id=?")->execute([$_POST['status'], (int)$_POST['project_id']]);
        $msg = 'Project status updated.';
    }
}

// Delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([(int)$_GET['delete']]);
    $msg = 'Project deleted.';
}

// Filter
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

$sql = "SELECT p.*, u.name AS client_name, (SELECT COUNT(*) FROM offers WHERE project_id=p.id) AS offer_count
        FROM projects p JOIN users u ON p.client_id=u.id WHERE 1";
$params = [];
if ($search) { $sql .= " AND (p.title LIKE ? OR u.name LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($status) { $sql .= " AND p.status=?"; $params[] = $status; }
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Manage Projects</title>
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
        <a href="projects.php" class="active-link">Projects</a>
        <a href="offers.php">Offers</a>
        <a href="messages.php">Messages</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">
        <div class="section-title"><span>Admin Panel</span><h2>Manage Projects</h2></div>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by title or client..." value="<?= htmlspecialchars($search) ?>" class="filter-input">
            <select name="status" class="filter-select">
                <option value="">All Statuses</option>
                <option value="open" <?= $status==='open'?'selected':'' ?>>Open</option>
                <option value="in_progress" <?= $status==='in_progress'?'selected':'' ?>>In Progress</option>
                <option value="completed" <?= $status==='completed'?'selected':'' ?>>Completed</option>
                <option value="cancelled" <?= $status==='cancelled'?'selected':'' ?>>Cancelled</option>
            </select>
            <button type="submit" class="primary-btn btn-sm">Filter</button>
            <?php if ($search || $status): ?><a href="projects.php" class="secondary-btn btn-sm">Clear</a><?php endif; ?>
        </form>

        <div class="form-card">
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Title</th><th>Client</th><th>Budget</th><th>Offers</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php if (empty($projects)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:30px;">No projects found.</td></tr>
                <?php else: ?>
                    <?php foreach ($projects as $p):
                        $bc = match($p['status']) { 'open'=>'badge-open','in_progress'=>'badge-progress','completed'=>'badge-accepted', default=>'badge-rejected' };
                    ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars(substr($p['title'],0,35)) ?></td>
                        <td style="color:var(--text-muted)"><?= htmlspecialchars($p['client_name']) ?></td>
                        <td style="color:var(--pink-light)">$<?= number_format($p['budget'],2) ?></td>
                        <td><?= $p['offer_count'] ?></td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="project_id" value="<?= $p['id'] ?>">
                                <select name="status" class="filter-select" style="padding:5px 10px;font-size:12px;" onchange="this.form.submit()">
                                    <?php foreach (['open','in_progress','completed','cancelled'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $p['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="?delete=<?= $p['id'] ?>" class="btn-reject btn-sm" onclick="return confirm('Delete this project and all its offers?')">Delete</a>
                        </td>
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
