<?php
include 'layout/head.php';
include 'lib/connection.php';
include '../adminDashboard/common/validation.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../adminDashboard/login.php");
    exit;
}

$data = [];
$errors = [];
$uploadDir = 'assets/upload/post/';

if (isset($_POST['action'])) {
    $title = Validation($_POST['title']);
    $description = Validation($_POST['description']);
    if (empty($title)) {
        $errors['title'] = 'Post title is required.';
    } else {
        $data['title'] = $title;
    }

    if (empty($description)) {
        $errors['description'] = 'Description is required.'; // More informative error
    } else {
        $data['description'] = $description;
    }


    if (!empty($_FILES['image_url']['name'])) {
        $imageName = $_FILES['image_url']['name'];
        $imageTmp = $_FILES['image_url']['tmp_name'];
        $imageSize = $_FILES['image_url']['size'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageExt, $allowed)) {
            $errors['image_url'] = "Only JPG, JPEG, & PNG files are allowed.";
        } elseif ($imageSize > 3 * 1024 * 1024) { // Check size
            $errors['image_url'] = "File size must be less than 3MB.";
        } else {

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = uniqid('POST', true) . '.' . $imageExt;
            $uploadPath = $uploadDir . $newFileName; // Define full path

            if (move_uploaded_file($imageTmp, $uploadPath)) {
                $data['image_url'] = $newFileName;


                if (!empty($user['image_url']) && file_exists($uploadDir . $user['image_url'])) {
                    unlink($uploadDir . $user['image_url']);
                }
            } else {
                $errors['image_url'] = "Failed to upload image.";
            }
        }
    } else {

        $data['image_url'] = $user['image_url'] ?? null;
    }


    if (empty($errors)) {
        print_r($data);

    }

}
?>

<body>
    <?php include 'navBar.php'; ?>

    <?= $_SESSION['user_id'] ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4 fw-bold text-primary">Create New Post</h1>
                <p class="text-muted mb-4">Fill in all the required details to publish your blog post.</p>

                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post"
                    enctype="multipart/form-data">

                    <div class="mb-4">
                        <label for="postTitle" class="form-label fw-bold">Post Title <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="postTitle" name="title"
                            placeholder="Enter an engaging title for your post..."
                            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        <div class="text-danger"><?= $errors['title'] ?? '' ?></div>

                    </div>

                    <div class="mb-4">
                        <label for="postBody" class="form-label fw-bold">Post Content (Body) <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="postBody" name="description" rows="15"
                            placeholder="Write your detailed article or story here..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <div class="text-danger"><?= $errors['description'] ?? '' ?></div>
                    </div>

                    <div class="mb-4">
                        <label for="postImage" class="form-label fw-bold">Upload Featured Image</label>
                        <input class="form-control" type="file" id="postImage" name="image_url">
                        <div class="text-danger"><?= $errors['image_url'] ?? '' ?></div>
                    </div>


                    <?php
                    if (isset($_SESSION['user_id'])) {
                        $query = "SELECT * FROM users WHERE id = :id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
                    ?>
                    <div class="row g-3 mb-4" hidden>
                        <div class="col-md-6">
                            <label for="authorName" class="form-label fw-bold">Author Name</label>
                            <input type="text" class="form-control" id="authorName" name="author_name"
                                value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="authorEmail" class="form-label fw-bold">Author Email</label>
                            <input type="email" class="form-control" id="authorEmail" name="author_email"
                                value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>
                    </div>

                    <hr class="my-5">
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-save me-2"></i> Save as Draft
                        </button>
                        <button type="submit" name="action" value="publish" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-send me-2"></i> Publish Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>