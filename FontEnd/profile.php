<?php 
include 'layout/head.php';
include 'navBar.php';

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../adminbashboard/login.php");
    exit;
}

// ✅ Fetch user data
include 'lib/connection.php';
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// যদি user না থাকে (মানে ডাটাবেজ থেকে delete করা হয়েছে)
// if (!$user) {
//     // সেশন destroy করে logout করো
//     $_SESSION = [];
//     session_destroy();
//     header("Location: login.php?error=user_deleted");
//     exit;
// }
?>

<!-- Profile Dashboard -->
<div class="container my-5">
    <div class="card shadow border-0 mx-auto" style="max-width: 850px;">
        <div class="card-body p-5">

            <!-- Profile Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <img 
                        src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/120' ?>" 
                        alt="Profile" 
                        class="rounded-circle border border-3 border-primary me-4"
                        width="100" height="100">
                    <div>
                        <h3 class="card-title mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                        <p class="text-muted mb-0"><i class="bi bi-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>

                <!-- Logout Button -->
                <div>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm px-4">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Account Details -->
            <hr>
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-person-lines-fill me-1"></i> Account Details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-light">
                        <strong>Username:</strong> <br> <?= htmlspecialchars($user['name']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-light">
                        <strong>Phone:</strong> <br> <?= htmlspecialchars($user['phone'] ?? 'Not set') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-light">
                        <strong>Role:</strong> <br> <?= htmlspecialchars($user['role'] ?? 'User') ?>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4">
                <a href="edit_profile.php" class="btn btn-primary me-2">
                    <i class="bi bi-pencil-square me-1"></i> Edit Profile
                </a>
                <a href="change_password.php" class="btn btn-secondary">
                    <i class="bi bi-shield-lock me-1"></i> Change Password
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
