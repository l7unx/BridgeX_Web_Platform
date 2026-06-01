<?php
require_once '../includes/auth.php';
requireRole('developer');

$pdo   = getDB();
$devId = getUserId();

$success = '';
$error   = '';

// Submit project
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_project_offer_id'])) {
    $offerId = (int)$_POST['submit_project_offer_id'];

    try {
        $stmt = $pdo->prepare("
            UPDATE projects p
            JOIN offers o ON o.project_id = p.id
            SET p.status = 'completed'
            WHERE o.id = ?
              AND o.developer_id = ?
              AND o.status = 'accepted'
        ");

        $stmt->execute(array($offerId, $devId));

        if ($stmt->rowCount() > 0) {
            $success = 'Project submitted successfully.';
        } else {
            $error = 'Unable to submit this project.';
        }
    } catch (Exception $e) {
        $error = 'An error occurred while submitting the project.';
    }
}

// Fetch developer offers
$stmt = $pdo->prepare("
    SELECT 
        o.*,
        p.title AS project_title,
        p.description AS project_description,
        p.budget AS project_budget,
        p.duration AS project_duration,
        p.project_type,
        p.status AS project_status,
        u.name AS client_name,
        u.email AS client_email
    FROM offers o
    JOIN projects p ON o.project_id = p.id
    JOIN users u ON p.client_id = u.id
    WHERE o.developer_id = ?
    ORDER BY o.created_at DESC
");

$stmt->execute(array($devId));
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$offerStatusLabels = array(
        'pending' => array(
                'label' => 'Pending',
                'class' => 'badge-pending'
        ),
        'accepted' => array(
                'label' => 'Accepted',
                'class' => 'badge-accepted'
        ),
        'rejected' => array(
                'label' => 'Rejected',
                'class' => 'badge-rejected'
        )
);

$projectStatusLabels = array(
        'open' => array(
                'label' => 'Open',
                'class' => 'badge-open'
        ),
        'in_progress' => array(
                'label' => 'In Progress',
                'class' => 'badge-progress'
        ),
        'completed' => array(
                'label' => 'Completed',
                'class' => 'badge-completed'
        ),
        'cancelled' => array(
                'label' => 'Cancelled',
                'class' => 'badge-closed'
        )
);

$typeLabels = array(
        'web' => 'Website',
        'mobile' => 'Mobile App',
        'design' => 'UI/UX Design',
        'backend' => 'Backend / API',
        'ecommerce' => 'E-commerce Store',
        'other' => 'Other'
);
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
        <a href="reviews.php">Reviews</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">

        <div class="page-hero" style="text-align:left; padding:40px 0 20px;">
            <div class="hero-badge">📄 My Offers</div>
            <h1 style="margin-top:12px; font-size:36px;">Track your <span class="pink-text">submitted offers</span></h1>
            <p style="color:var(--text-muted); margin-top:8px;">
                View your submitted offers, check their status, and submit completed projects.
            </p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($offers)): ?>
            <div class="empty-state glass-panel">
                <div style="font-size:48px;">📭</div>
                <h3>No offers submitted yet</h3>
                <p style="color:var(--text-muted); margin:12px 0 20px;">
                    Browse available projects and submit your first offer.
                </p>
                <a href="browse_projects.php" class="primary-btn">Browse Projects</a>
            </div>
        <?php else: ?>

            <div class="offers-grid">
                <?php foreach ($offers as $o): ?>
                    <?php
                    $offerStatus = isset($o['status']) ? $o['status'] : 'pending';
                    $projectStatus = isset($o['project_status']) ? $o['project_status'] : 'open';

                    if (isset($offerStatusLabels[$offerStatus])) {
                        $offerBadge = $offerStatusLabels[$offerStatus];
                    } else {
                        $offerBadge = array(
                                'label' => $offerStatus,
                                'class' => 'badge-pending'
                        );
                    }

                    if (isset($projectStatusLabels[$projectStatus])) {
                        $projectBadge = $projectStatusLabels[$projectStatus];
                    } else {
                        $projectBadge = array(
                                'label' => $projectStatus,
                                'class' => 'badge-open'
                        );
                    }

                    $projectType = isset($o['project_type']) ? $o['project_type'] : '';
                    $projectTypeLabel = isset($typeLabels[$projectType]) ? $typeLabels[$projectType] : $projectType;
                    ?>

                    <div class="offer-card glass-panel">
                        <div class="offer-card-header">
                            <div>
                                <h3 style="margin-bottom:8px;">
                                    <?= htmlspecialchars($o['project_title']) ?>
                                </h3>

                                <div style="font-size:13px; color:var(--text-muted);">
                                    Client: <?= htmlspecialchars($o['client_name']) ?>
                                    <?php if (!empty($o['client_email'])): ?>
                                        · <?= htmlspecialchars($o['client_email']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <span class="badge <?= $offerBadge['class'] ?>" style="margin-left:auto;">
                                <?= htmlspecialchars($offerBadge['label']) ?>
                            </span>
                        </div>

                        <div style="margin-top:12px;">
                            <span class="badge <?= $projectBadge['class'] ?>">
                                Project: <?= htmlspecialchars($projectBadge['label']) ?>
                            </span>
                        </div>

                        <div class="offer-details-row" style="margin-top:16px;">
                            <span class="offer-detail">
                                <strong>💰 Your Price:</strong>
                                <?= htmlspecialchars($o['price']) ?> SAR
                            </span>

                            <span class="offer-detail">
                                <strong>⏱ Delivery Time:</strong>
                                <?= htmlspecialchars($o['delivery_time']) ?>
                            </span>
                        </div>

                        <div class="offer-details-row">
                            <span class="offer-detail">
                                <strong>📌 Project Type:</strong>
                                <?= htmlspecialchars($projectTypeLabel) ?>
                            </span>

                            <span class="offer-detail">
                                <strong>💼 Project Budget:</strong>
                                <?= htmlspecialchars($o['project_budget']) ?> SAR
                            </span>
                        </div>

                        <p class="offer-message">
                            <?= nl2br(htmlspecialchars($o['message'])) ?>
                        </p>

                        <div style="margin-top:14px;">
                            <a href="project_details.php?id=<?= $o['project_id'] ?>" class="secondary-btn btn-sm">
                                View Project Details
                            </a>
                        </div>

                        <?php if ($offerStatus === 'accepted' && $projectStatus !== 'completed'): ?>
                            <div style="color:#7ee8e2; font-size:13px; font-weight:600; margin-top:14px;">
                                🎉 Congratulations! Your offer was accepted.
                            </div>

                            <form method="POST" style="margin-top:12px;">
                                <input type="hidden" name="submit_project_offer_id" value="<?= $o['id'] ?>">
                                <button type="submit" class="primary-btn">
                                    Submit Project
                                </button>
                            </form>

                        <?php elseif ($offerStatus === 'accepted' && $projectStatus === 'completed'): ?>
                            <div style="color:#7ee8e2; font-size:13px; font-weight:600; margin-top:14px;">
                                ✅ Project submitted successfully. Waiting for client review.
                            </div>

                        <?php elseif ($offerStatus === 'rejected'): ?>
                            <div style="color:#ff8b8b; font-size:13px; font-weight:600; margin-top:14px;">
                                ❌ Your offer was not selected for this project.
                            </div>

                        <?php else: ?>
                            <div style="color:var(--text-muted); font-size:13px; font-weight:600; margin-top:14px;">
                                ⏳ Waiting for the client to accept your offer.
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </section>
</main>

<footer class="site-footer">
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

</body>
</html>