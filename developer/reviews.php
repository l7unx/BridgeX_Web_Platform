<?php
require_once '../includes/auth.php';
requireRole('developer');

$pdo = getDB();
$devId = getUserId();

$stmt = $pdo->prepare("
    SELECT 
        r.rating,
        r.comment,
        r.created_at,
        p.title AS project_title,
        u.name AS client_name
    FROM reviews r
    JOIN projects p ON r.project_id = p.id
    JOIN users u ON r.client_id = u.id
    WHERE r.developer_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$devId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Client Reviews</title>
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
        <a href="my_offers.php">My Offers</a>
        <a href="reviews.php" class="active-link">Reviews</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">
        <div class="section-title">
            <span>Client Feedback</span>
            <h2>Client Reviews</h2>
        </div>

        <?php if (empty($reviews)): ?>
            <div class="empty-state form-card">
                <div style="font-size:48px;">⭐</div>
                <h3>No Reviews Yet</h3>
                <p style="color:var(--text-muted)">Client reviews will appear here after accepted projects are rated.</p>
                <a href="dashboard.php" class="primary-btn" style="margin-top:20px;display:inline-block;">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="reviews-grid">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-card-header">
                            <div class="icon-box">⭐</div>
                            <div>
                                <h3><?= htmlspecialchars($review['project_title']) ?></h3>
                                <p class="review-client">Client: <?= htmlspecialchars($review['client_name']) ?></p>
                            </div>
                        </div>

                        <div class="review-stars">
                            <?php
                            $rating = (int)$review['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $rating ? '★' : '☆';
                            }
                            ?>
                        </div>

                        <p class="review-comment">
                            <?= htmlspecialchars($review['comment']) ?>
                        </p>

                        <span class="review-date">
                            <?= htmlspecialchars($review['created_at']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:25px;">
                <a href="dashboard.php" class="secondary-btn">Back to Dashboard</a>
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
            <a href="dashboard.php">Dashboard</a>
            <a href="browse_projects.php">Browse Projects</a>
            <a href="my_offers.php">My Offers</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

</body>
</html>