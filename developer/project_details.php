<?php
require_once '../includes/auth.php';
requireRole('developer');

$pdo   = getDB();
$devId = getUserId();

$success = '';
$error   = '';

$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($projectId <= 0) {
    header('Location: browse_projects.php');
    exit;
}

/*
    Get project details.
    The developer can view:
    - open projects
    - or projects they already submitted an offer to
*/
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        u.name AS client_name,
        u.email AS client_email
    FROM projects p
    JOIN users u ON p.client_id = u.id
    WHERE p.id = ?
      AND (
          p.status = 'open'
          OR EXISTS (
              SELECT 1
              FROM offers o
              WHERE o.project_id = p.id
                AND o.developer_id = ?
          )
      )
");
$stmt->execute(array($projectId, $devId));
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header('Location: browse_projects.php');
    exit;
}

// Check if this developer already submitted an offer
$offerStmt = $pdo->prepare("
    SELECT *
    FROM offers
    WHERE project_id = ? AND developer_id = ?
    LIMIT 1
");
$offerStmt->execute(array($projectId, $devId));
$existingOffer = $offerStmt->fetch(PDO::FETCH_ASSOC);

// Submit offer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_offer'])) {
    $price        = isset($_POST['price']) ? trim($_POST['price']) : '';
    $deliveryTime = isset($_POST['delivery_time']) ? trim($_POST['delivery_time']) : '';
    $message      = isset($_POST['message']) ? trim($_POST['message']) : '';

    if ($existingOffer) {
        $error = 'You have already submitted an offer for this project.';
    } elseif ($project['status'] !== 'open') {
        $error = 'You can only submit offers for open projects.';
    } elseif (!$price || !$deliveryTime || !$message) {
        $error = 'Please fill in all required fields.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Please enter a valid price.';
    } elseif (strlen($message) < 20) {
        $error = 'Cover message must be at least 20 characters.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO offers 
                (project_id, developer_id, price, delivery_time, message, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");

            $stmt->execute(array(
                    $projectId,
                    $devId,
                    $price,
                    $deliveryTime,
                    $message
            ));

            $success = 'Your offer has been submitted successfully.';

            $offerStmt = $pdo->prepare("
                SELECT *
                FROM offers
                WHERE project_id = ? AND developer_id = ?
                LIMIT 1
            ");
            $offerStmt->execute(array($projectId, $devId));
            $existingOffer = $offerStmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            $error = 'An error occurred while submitting your offer.';
        }
    }
}

$typeLabels = array(
        'web' => 'Website',
        'mobile' => 'Mobile App',
        'design' => 'UI/UX Design',
        'backend' => 'Backend / API',
        'ecommerce' => 'E-commerce Store',
        'other' => 'Other'
);

$statusLabels = array(
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

$projectType = isset($project['project_type']) ? $project['project_type'] : '';
$projectTypeLabel = isset($typeLabels[$projectType]) ? $typeLabels[$projectType] : $projectType;

$projectStatus = isset($project['status']) ? $project['status'] : 'open';
$projectStatusData = isset($statusLabels[$projectStatus])
        ? $statusLabels[$projectStatus]
        : array('label' => $projectStatus, 'class' => 'badge-open');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BridgeX — Project Details</title>

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
        <a href="reviews.php">Reviews</a>
        <a href="../logout.php" class="nav-btn">Logout</a>
    </nav>
</header>

<main>
    <section class="section">

        <div class="page-hero" style="text-align:left; padding:40px 0 20px;">
            <div class="hero-badge">📌 Project Details</div>
            <h1 style="margin-top:12px; font-size:36px;">
                <?= htmlspecialchars($project['title']) ?>
            </h1>
            <p style="color:var(--text-muted); margin-top:8px;">
                Review project requirements and submit a suitable offer.
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

        <div class="glass-panel form-card">
            <div style="display:flex; justify-content:space-between; gap:16px; align-items:flex-start; flex-wrap:wrap;">
                <div>
                    <span class="badge <?= $projectStatusData['class'] ?>">
                        <?= htmlspecialchars($projectStatusData['label']) ?>
                    </span>

                    <h2 style="margin-top:14px;">
                        <?= htmlspecialchars($project['title']) ?>
                    </h2>

                    <p style="color:var(--text-muted); margin-top:6px;">
                        Client: <?= htmlspecialchars($project['client_name']) ?>
                        <?php if (!empty($project['client_email'])): ?>
                            · <?= htmlspecialchars($project['client_email']) ?>
                        <?php endif; ?>
                    </p>
                </div>

                <a href="browse_projects.php" class="secondary-btn btn-sm">← Back</a>
            </div>

            <div class="offer-details-row" style="margin-top:22px;">
                <span class="offer-detail">
                    <strong>📌 Category:</strong>
                    <?= htmlspecialchars($projectTypeLabel) ?>
                </span>

                <span class="offer-detail">
                    <strong>💰 Budget:</strong>
                    <?= htmlspecialchars($project['budget']) ?> SAR
                </span>
            </div>

            <div class="offer-details-row">
                <span class="offer-detail">
                    <strong>⏱ Duration:</strong>
                    <?= htmlspecialchars($project['duration']) ?>
                </span>

                <span class="offer-detail">
                    <strong>📅 Posted:</strong>
                    <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                </span>
            </div>

            <div style="margin-top:24px;">
                <h3 class="form-section-title">Project Description</h3>
                <p style="color:var(--text-main); line-height:1.8;">
                    <?= nl2br(htmlspecialchars($project['description'])) ?>
                </p>
            </div>

            <?php if (!empty($project['features'])): ?>
                <div style="margin-top:24px;">
                    <h3 class="form-section-title">Requirements / Notes</h3>
                    <p style="color:var(--text-main); line-height:1.8;">
                        <?= nl2br(htmlspecialchars($project['features'])) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($existingOffer): ?>
            <?php
            $offerStatus = isset($existingOffer['status']) ? $existingOffer['status'] : 'pending';
            $offerStatusData = isset($offerStatusLabels[$offerStatus])
                    ? $offerStatusLabels[$offerStatus]
                    : array('label' => $offerStatus, 'class' => 'badge-pending');
            ?>

            <div class="glass-panel form-card" style="margin-top:24px;">
                <h3 class="form-section-title">Your Submitted Offer</h3>

                <span class="badge <?= $offerStatusData['class'] ?>">
                    <?= htmlspecialchars($offerStatusData['label']) ?>
                </span>

                <div class="offer-details-row" style="margin-top:18px;">
                    <span class="offer-detail">
                        <strong>💰 Price:</strong>
                        <?= htmlspecialchars($existingOffer['price']) ?> SAR
                    </span>

                    <span class="offer-detail">
                        <strong>⏱ Delivery Time:</strong>
                        <?= htmlspecialchars($existingOffer['delivery_time']) ?>
                    </span>
                </div>

                <p class="offer-message">
                    <?= nl2br(htmlspecialchars($existingOffer['message'])) ?>
                </p>

                <?php if ($offerStatus === 'accepted' && $projectStatus !== 'completed'): ?>
                    <div style="color:#7ee8e2; font-size:13px; font-weight:600; margin-top:14px;">
                        🎉 Your offer was accepted. Go to My Offers to submit the project.
                    </div>
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
                        ⏳ Waiting for the client to review your offer.
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($projectStatus === 'open'): ?>
            <div class="glass-panel form-card" style="margin-top:24px;">
                <h3 class="form-section-title">Submit Your Offer</h3>

                <form method="POST" id="offerForm" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Price (SAR) <span class="required">*</span></label>
                            <input type="number" name="price" id="price" placeholder="e.g. 500" min="1"
                                   value="<?= htmlspecialchars(isset($_POST['price']) ? $_POST['price'] : '') ?>">
                            <span class="field-error" id="price-error"></span>
                        </div>

                        <div class="form-group">
                            <label>Delivery Time <span class="required">*</span></label>
                            <input type="text" name="delivery_time" id="delivery_time" placeholder="e.g. 2 weeks"
                                   value="<?= htmlspecialchars(isset($_POST['delivery_time']) ? $_POST['delivery_time'] : '') ?>">
                            <span class="field-error" id="delivery-error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Cover Message <span class="required">*</span></label>
                        <textarea name="message" id="message" rows="5"
                                  placeholder="Describe your approach, experience, and why you're the best fit..."><?= htmlspecialchars(isset($_POST['message']) ? $_POST['message'] : '') ?></textarea>
                        <span class="field-error" id="message-error"></span>
                    </div>

                    <button type="submit" name="submit_offer" class="primary-btn">
                        Submit Offer
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="glass-panel form-card" style="margin-top:24px;">
                <h3 class="form-section-title">Offer Closed</h3>
                <p style="color:var(--text-muted);">
                    This project is no longer open for new offers.
                </p>
            </div>
        <?php endif; ?>

    </section>
</main>

<footer class="site-footer">
    <div class="footer-bottom">© 2026 BridgeX Platform — All rights reserved</div>
</footer>

<script src="../assets/js/script.js"></script>
</body>
</html>