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
        <div class="card shadow-lg border-0 mx-auto" style="max-width: 900px; border-radius: 20px; overflow: hidden;">

            <!-- Gradient Header -->
            <div class="p-5 text-white text-center" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
                <div class="mb-3">
                    <div class="rounded-circle overflow-hidden mx-auto" style="width: 120px; height: 120px; border: 4px solid rgba(255,255,255,0.7);">
                        <img src="assets/upload/profile/<?= !empty($user['image']) ? htmlspecialchars($user['image']) : 'https://img.daisyui.com/images/profile/demo/batperson@192.webp' ?>" 
                            alt="Profile" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
                <h2 class="fw-bold"><?= htmlspecialchars($user['name']) ?></h2>
                <p class="mb-0"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email']) ?></p>
                <div class="mt-3">
                    <a href="logout.php" class="btn btn-outline-light btn-sm px-4">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Account Details -->
            <div class="card-body p-5 bg-white">
                <h5 class="fw-bold text-primary mb-4"><i class="bi bi-person-lines-fill me-2"></i>Account Details</h5>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded shadow-sm bg-light hover-shadow transition">
                            <strong>Username:</strong> <br> <?= htmlspecialchars($user['name']) ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded shadow-sm bg-light hover-shadow transition">
                            <strong>Phone:</strong> <br> <?= htmlspecialchars($user['number'] ?? 'Not set') ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded shadow-sm bg-light hover-shadow transition">
                            <strong>Role:</strong> <br> <?= htmlspecialchars($user['role'] ?? 'User') ?>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-5 d-flex flex-wrap gap-3">
                    <a href="edit_profile.php?id=<?= base64_encode($user['id']) ?>" class="btn btn-gradient-primary text-white px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </a>
                    <a href="change_password.php" class="btn btn-outline-primary px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-shield-lock me-1"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Styles -->
    <style>
    .hover-shadow:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    .btn-gradient-primary {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        border: none;
    }
    </style>