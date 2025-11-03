
<?php
session_start();
include 'lib/connection.php';
$errors = [];
$data = [];
if(isset($_POST['submit'])){
    
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'layout/head.php' ?>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e3f2fd 0%, #b2ebf2 100%);
    
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .form-container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border: 1px solid #e0e0e0;
      border-radius: 1.2rem;
      padding: 2rem;
      width: 100%;
      max-width: 650px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    .form-container:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }
    .form-title {
      color: #0288d1;
      font-weight: 600;
      text-transform: uppercase;
      
    }
    .form-label {
      font-weight: 500;
      color: #37474f;
    }
    .btn-primary {
      background: #0288d1;
      border: none;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background: #0277bd;
      box-shadow: 0 4px 12px rgba(2, 119, 189, 0.3);
    }
  </style>
</head>

<body>
  <section class="form-container">
    <h1 class="form-title">Update your Profile</h1>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">

      <!-- Hidden ID -->
      <input type="hidden" name="usersId" value="<?= $user['id'] ?? '' ?>">

      <!-- Name -->
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" placeholder="Enter your name"
               value="<?= $user['name'] ?? '' ?>">
        <div class="text-danger small"><?= $errors['name'] ?? '' ?></div>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Enter your email"
               value="<?= $user['email'] ?? '' ?>">
        <div class="text-danger small"><?= $errors['email'] ?? '' ?></div>
      </div>

      <!-- File Upload -->
      <div class="mb-3">
        <label class="form-label">Profile Image</label>
        <input type="file" name="file" class="form-control">
        <div class="text-danger small"><?= $errors['file'] ?? '' ?></div>
      </div>

      <!-- Phone -->
      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" placeholder="Enter your phone number"
               value="<?= $user['phone'] ?? '' ?>">
        <div class="text-danger small"><?= $errors['phone'] ?? '' ?></div>
      </div>

      <!-- Submit Button -->
      <button type="submit" name="submit" class="btn btn-primary w-100 py-2">Update</button>
    </form>
  </section>

  <!-- ✅ Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
