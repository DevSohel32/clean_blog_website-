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
if (isset($_post['action'])) {
    $title = Validation($_GET['title']);
    $description = Validation($_GET['description']);
    echo $title;
    echo $description;
    if (empty($title)) {
        $errors['title'] = 'post title is required.';
    } else {
        $data['title'] = $title;
    }
    if (empty($description)) {
        $errors['description'] = 'Description';
    } else {
        $data['description'] = $description;
    }
    if (!empty($_FILES['image_url']['name'])) {
        $imageName = $_FILES['image_url']['name'];
        $imageTmp = $_FILES['image_url']['tmp_name'];
        $imageSize = $_FILES['image_url']['size'];
    }

}
?>

<body>
    <?php include 'navBar.php'; ?>
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
                            placeholder="Enter an engaging title for your post...">

                    </div>

                    <div class="mb-4">
                        <label for="postBody" class="form-label fw-bold">Post Content (Body) <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="postBody" name="description" rows="15"
                            placeholder="Write your detailed article or story here..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="postImage" class="form-label fw-bold">Upload Featured Image</label>
                        <input class="form-control" type="file" id="postImage" name="image_url">

                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="authorName" class="form-label fw-bold">Author Name</label>
                            <input type="text" class="form-control" id="authorName" name="author_name"
                                value="{{ logged_in_user.name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="authorEmail" class="form-label fw-bold">Author Email</label>
                            <input type="email" class="form-control" id="authorEmail" name="author_email"
                                value="{{ user.email }}" readonly>
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