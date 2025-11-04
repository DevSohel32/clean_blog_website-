<?php
include '../adminbashboard/common/validation.php';
include 'lib/connection.php';
session_start();

$errors = [];
$data = [];

// Fetch user data
if (isset($_GET['id'])) {
    $id = base64_decode($_GET['id']);
    $query = "SELECT * FROM users WHERE id=:id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['submit'])) {
    $id = Validation($_POST['id']);
    $name = Validation($_POST['name']);
    $email = Validation($_POST['email']);
    $phone = Validation($_POST['phone']);
    $password = $_POST['password'];

    // Retain input values
    $data['name'] = $name;
    $data['email'] = $email;
    $data['phone'] = $phone;
    $data['password'] = $password;

    // Validation
    if (empty($name)) $errors['name'] = "Name is required.";
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    if (empty($phone)) $errors['phone'] = "Phone is required.";

    // Image upload
    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageSize = $_FILES['image']['size'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageExt, $allowed)) {
            $errors['image'] = "Only JPG, JPEG, & PNG files are allowed.";
        } else {
            $uploadDir = 'assets/upload/profile/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newFileName = uniqid('IMG_', true) . '.' . $imageExt;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($imageTmp, $uploadPath)) {
                $data['image'] = $newFileName;

                // Optional: delete old image
                if (!empty($user['image']) && file_exists($uploadDir . $user['image'])) {
                    unlink($uploadDir . $user['image']);
                }
            } elseif ($imageSize > 3 * 1024 * 1024) {
                $errors['image'] = "File size must be less than 3MB.";
            } else {
                $errors['image'] = "Failed to upload image.";
            }
        }
    } else {
        $data['image'] = $user['image'] ?? null;
    }

    // Update DB if no validation errors
    if (empty($errors)) {
        $query = "UPDATE users 
          SET name = :name, 
              email = :email, 
              phone = :phone, 
              image_url = :image_url, 
              updated_at = NOW() 
          WHERE id = :id";

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", password_hash=:password_hash";
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':image_url', $data['image']);
        if (!empty($password)) $stmt->bindParam(':password_hash', $hashedPassword);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit;
        } else {
            $errors['database'] = "Update failed. Please try again.";
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
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(15px);
    border-radius: 1.5rem;
    padding: 2.5rem;
    width: 100%;
    max-width: 700px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}
.form-title {color:#0288d1; font-weight:700; text-align:center; margin-bottom:2rem;}
.form-label {font-weight:500; color:#37474f;}
.form-control {border-radius:0.8rem; padding:0.6rem 1rem;}
.form-control:focus {border-color:#0288d1; box-shadow:0 0 8px rgba(2,136,209,0.2);}
.btn-primary {background:linear-gradient(135deg,#0288d1,#0277bd); border:none; border-radius:1rem; font-weight:600;}
.btn-primary:hover {transform:scale(1.05);}
img.profile-preview {border-radius:1rem; margin-top:0.5rem; border:2px solid #0288d1;}
.text-danger {font-size:0.85rem; margin-top:0.2rem;}
</style>
</head>
<body>
<section class="form-container">
<h1 class="form-title">Update Your Profile</h1>

<?php if (!empty($errors['database'])): ?>
    <div class="alert alert-danger"><?= $errors['database'] ?></div>
<?php endif; ?>

<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>">

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" 
               value="<?= htmlspecialchars($data['name'] ?? $user['name'] ?? '') ?>">
        <div class="text-danger"><?= $errors['name'] ?? '' ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" 
               value="<?= htmlspecialchars($data['email'] ?? $user['email'] ?? '') ?>">
        <div class="text-danger"><?= $errors['email'] ?? '' ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" 
               value="<?= htmlspecialchars($data['phone'] ?? $user['phone'] ?? '') ?>">
        <div class="text-danger"><?= $errors['phone'] ?? '' ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Profile Image</label>
        <input type="file" name="image" class="form-control">
        <?php if (!empty($data['image']) || !empty($user['image'])): ?>
            <img src="assets/upload/profile/<?= htmlspecialchars($data['image'] ?? $user['image']) ?>" 
                 alt="Profile" class="profile-preview" width="120">
        <?php endif; ?>
        <div class="text-danger"><?= $errors['image'] ?? '' ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">New Password (leave blank to keep current)</label>
        <input type="password" name="password" class="form-control">
    </div>

    <button type="submit" name="submit" class="btn btn-primary w-100 py-2 mt-3">Update</button>
</form>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
