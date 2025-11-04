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

    <div class="card shadow-lg border-0 mx-auto" style="max-width: 900px; border-radius: 25px; overflow: hidden;">
        
        <!-- ✅ Success Message -->
        <?php if (isset($_SESSION['success'])) { ?>
            <div role="alert" class="alert alert-success mb-6 shadow-md d-flex align-items-center gap-3 rounded-3 p-3">
                <i class="bi bi-check-circle-fill fs-4"></i>
                <span class="fw-medium"><?= $_SESSION['success'] ?? '' ?></span>
            </div>
        <?php }
        unset($_SESSION['success']); ?>

        <!-- Gradient Header -->
        <div class="p-5 text-white text-center" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
            <div class="mb-3 position-relative">
                <div class="rounded-circle overflow-hidden mx-auto border border-white border-4"
                    style="width: 130px; height: 130px;">
                    <img src="assets/upload/profile/<?= !empty($user['image_url']) ? htmlspecialchars($user['image_url']) : 'https://img.daisyui.com/images/profile/demo/batperson@192.webp' ?>"
                        alt="Profile" class="w-100 h-100 object-fit-cover">
                </div>
                <!-- Online Badge -->
                <span class="position-absolute bottom-0 start-50 translate-middle-x bg-success rounded-circle border border-white" 
                    style="width: 20px; height: 20px;"></span>
            </div>
            <h2 class="fw-bold"><?= htmlspecialchars($user['name']) ?></h2>
            <p class="mb-0"><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($user['email']) ?></p>
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
                    <div class="p-4 border rounded-4 shadow-sm bg-light hover-shadow transition text-center">
                        <small class="text-muted">Username</small>
                        <div class="fw-bold fs-5 mt-1"><?= htmlspecialchars($user['name']) ?></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 border rounded-4 shadow-sm bg-light hover-shadow transition text-center">
                        <small class="text-muted">Phone</small>
                        <div class="fw-bold fs-5 mt-1"><?= htmlspecialchars($user['phone'] ?? 'Not set') ?></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 border rounded-4 shadow-sm bg-light hover-shadow transition text-center">
                        <small class="text-muted">Role</small>
                        <div class="fw-bold fs-5 mt-1"><?= htmlspecialchars($user['role'] ?? 'User') ?></div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-5 d-flex flex-wrap gap-3 justify-content-center">
                <a href="edit_profile.php?id=<?= base64_encode($user['id']) ?>"
                    class="btn btn-gradient-primary text-white px-5 py-2 rounded-pill shadow-sm fw-semibold">
                    <i class="bi bi-pencil-square me-2"></i> Edit Profile
                </a>
                <a href="change_password.php" class="btn btn-outline-primary px-5 py-2 rounded-pill shadow-sm fw-semibold">
                    <i class="bi bi-shield-lock me-2"></i> Change Password
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Custom Styles -->
<style>
    /* Card Hover Shadow */
    .hover-shadow:hover {
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.18);
        transform: translateY(-4px);
        transition: all 0.3s ease;
    }

    /* Gradient Buttons */
    .btn-gradient-primary {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        border: none;
    }

    /* Smooth transition for buttons */
    .btn-gradient-primary:hover {
        filter: brightness(1.1);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    /* Profile card text styles */
    .card-body small {
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
</style>
