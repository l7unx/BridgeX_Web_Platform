<?php
require_once '../includes/auth.php';
requireRole('admin');

$pdo = getDB();
$msg = '';

// Delete user
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $delId = (int)$_GET['delete'];
    if ($delId !== getUserId()) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$delId]);
        $msg = 'User deleted successfully.';
    } else {
        $msg = 'You cannot delete your own account.';
    }
}

// Search/filter
$search = trim($_GET['search'] ?? '');
$role   = trim($_GET['role'] ?? '');

$sql = "SELECT * FROM users WHERE 1";
$params = [];
if ($search) { $sql .= " AND (name LIKE ? OR email LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($role)   { $sql .= " AND role = ?"; $params[] = $role; }
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Manage Users</title>
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
        <a href="users.php" class="active-link">Users</a>
        <a href="projects.php">Projects</a>
        <a href="offers.php">Offers</a>
        <a href="messages.php">Messages</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">
        <div class="section-title"><span>Admin Panel</span><h2>Manage Users</h2></div>

        <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>" class="filter-input">
            <select name="role" class="filter-select">
                <option value="">All Roles</option>
                <option value="client" <?= $role==='client'?'selected':'' ?>>Client</option>
                <option value="developer" <?= $role==='developer'?'selected':'' ?>>Developer</option>
                <option value="admin" <?= $role==='admin'?'selected':'' ?>>Admin</option>
            </select>
            <button type="submit" class="primary-btn btn-sm">Filter</button>
            <?php if ($search || $role): ?><a href="users.php" class="secondary-btn btn-sm">Clear</a><?php endif; ?>
        </form>

        <div class="form-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:30px;">No users found.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u):
                        $roleColor = $u['role']==='admin' ? 'badge-accepted' : ($u['role']==='developer' ? 'badge-pending' : 'badge-open');
                    ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td style="color:var(--text-muted)"><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="badge <?= $roleColor ?>"><?= ucfirst($u['role']) ?></span></td>
                        <td style="color:var(--text-muted)"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <?php if ($u['id'] !== getUserId()): ?>
                                <a href="?delete=<?= $u['id'] ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>"
                                   class="btn-reject btn-sm"
                                   onclick="return confirm('Delete this user?')">Delete</a>
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:12px;">You</span>
                            <?php endif; ?>
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
