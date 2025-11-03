<?php

include '../adminbashboard/common/validation.php';

include 'lib/connection.php';
session_start();

$errors = [];
$data = [];

if (isset($_GET['id'])) {
    $id = base64_decode($_GET['id']);
    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['submit'])) {
    $id   = Validation($_POST['id']);
    $name   = Validation($_POST['name']);
    $email  = Validation($_POST['email']);
    $number = Validation($_POST['number']);
    $password = $_POST['password'];

    // Validation checks
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }else{
        $data['name'] = $name ;
    }
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }else{
        $data['email'] = $email;
    }
    if (empty($number)) {
        $errors['number'] = "Phone number is required.";
    }else {
    $pattern = '/^(\+880|880|00880|01)[3-9]\d{8}$/';

    if (!preg_match($pattern, $number)) {
        $errors['number'] = "Please enter a valid Bangladeshi mobile number (e.g., +88017XXXXXXXX).";
    }
    $data['number'] = $number;
}

    //  Handle image upload if provided
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
        $data['image'] = $user['image'] ?? null; 
    }
if (empty($errors)) {
     $query = "UPDATE users 
              SET name = :name,
                  email = :email,
                  image = :image,
                  number = :number,
                  password = :password
              WHERE id = :id";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':image', $data['image'], PDO::PARAM_STR);
    $stmt->bindParam(':number', $number, PDO::PARAM_STR); 
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
   
     if ($stmt->execute()) {
            $_SESSION['success'] = "update profile successful!";
            header("Location: profile.php");
            exit;
        } else {
            $errors['database'] = "Something went wrong. Please try again.";
        }
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
      background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(15px);
      border-radius: 1.5rem;
      padding: 2.5rem;
      width: 100%;
      max-width: 700px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .form-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
    }

    .form-title {
      color: #0288d1;
      font-weight: 700;
      text-transform: uppercase;
      text-align: center;
      letter-spacing: 1px;
      margin-bottom: 2rem;
    }

    .form-label {
      font-weight: 500;
      color: #37474f;
    }

    .form-control {
      border-radius: 0.8rem;
      padding: 0.6rem 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #0288d1;
      box-shadow: 0 0 8px rgba(2, 136, 209, 0.2);
    }

    .btn-primary {
      background: linear-gradient(135deg, #0288d1, #0277bd);
      border: none;
      border-radius: 1rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(2, 119, 189, 0.3);
    }

    img.profile-preview {
      border-radius: 1rem;
      margin-top: 0.5rem;
      border: 2px solid #0288d1;
      transition: all 0.3s ease;
    }

    img.profile-preview:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(2, 136, 209, 0.3);
    }

    .text-danger {
      font-size: 0.85rem;
      margin-top: 0.2rem;
    }
</style>
</head>

<body>
<section class="form-container">
  <h1 class="form-title">Update Your Profile</h1>

  <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>">

    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="<?= $user['name'] ?? '' ?>">
      <div class="text-danger"><?= $errors['name'] ?? '' ?></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= $user['email'] ?? '' ?>">
      <div class="text-danger"><?= $errors['email'] ?? '' ?></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Profile Image</label>
      <input type="file" name="image" class="form-control">
      <?php if (!empty($user['image'])): ?>
          <img src="assets/upload/profile/<?= $user['image'] ?>" alt="Profile" class="profile-preview" width="120">
      <?php endif; ?>
      <div class="text-danger"><?= $errors['image'] ?? '' ?></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input type="text" name="number" class="form-control" value="<?= $user['number'] ?? '' ?>">
      <div class="text-danger"><?= $errors['number'] ?? '' ?></div>
    </div>

    <div class="mb-3">
      <input type="text" name="password" class="form-control" hidden value="<?= $user['password'] ?? '' ?>">
    </div>

    <?php if (!empty($errors['db'])): ?>
      <div class="alert alert-danger"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <button type="submit" name="submit" class="btn btn-primary w-100 py-2 mt-3">Update</button>
  </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
