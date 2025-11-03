<?php

include '../adminbashboard/common/validation.php';

include 'lib/connection.php';
session_start();

$errors = [];
$data = [];

// Fetch logged-in user info if needed
// Example: $userId = $_SESSION['user_id']; 
// $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $userId"));

if (isset($_POST['submit'])) {
    $name   = Validation($_POST['name']);
    $email  = Validation($_POST['email']);
    $number = Validation($_POST['number']);

    // ✅ Validation checks
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($number)) {
        $errors['number'] = "Phone number is required.";
    }

    // ✅ Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmp  = $_FILES['image']['tmp_name'];
        $imageSize = $_FILES['image']['size'];
        $imageExt  = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageExt, $allowed)) {
            $errors['image'] = "Only JPG, JPEG, & PNG files are allowed.";
        } else {
            $uploadDir = 'assets/upload/profile/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = uniqid('IMG_', true) . '.' . $imageExt;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($imageTmp, $uploadPath)) {
                $data['image'] = $newFileName;

                // Optional: delete old image if exists
                // if (!empty($user['image']) && file_exists($uploadDir . $user['image'])) {
                //     unlink($uploadDir . $user['image']);
                // }
            }elseif ($imageSize > 3 * 1024 * 1024) { // 2MB limit
            $errors['image'] = "File size must be less than 2MB.";
        } else {
                $errors['image'] = "Failed to upload image.";
            }
        }
    } else {
        $data['image'] = $user['image'] ?? null; // keep old image if not uploaded
    }
 if(empty($errors)){
    $query = "INSERT * FROM user(name,email,image,number,password) VALUES(:name,:email,:image,:number,:password)";
    
 }
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
  <h1 class="form-title mb-4">Update Your Profile</h1>

  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="usersId" value="<?= $user['id'] ?? '' ?>">

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= $user['name'] ?? '' ?>">
      <div class="text-danger small"><?= $errors['name'] ?? '' ?></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= $user['email'] ?? '' ?>">
      <div class="text-danger small"><?= $errors['email'] ?? '' ?></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Profile Image</label>
      <input type="file" name="image" class="form-control">
      <?php if (!empty($user['image'])): ?>
          <img src="assets/upload/profile/<?= $user['image'] ?>" alt="Profile" class="mt-2 rounded" width="100">
      <?php endif; ?>
      <div class="text-danger small"><?= $errors['image'] ?? '' ?></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="number" class="form-control" value="<?= $user['number'] ?? '' ?>">
      <div class="text-danger small"><?= $errors['number'] ?? '' ?></div>
    </div>

    <?php if (!empty($errors['db'])): ?>
      <div class="alert alert-danger"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <button type="submit" name="submit" class="btn btn-primary w-100 py-2">Update</button>
  </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
