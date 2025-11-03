<?php
session_start();
include '../FontEnd/lib/connection.php';
$data = [];
$errors = [];

if (isset($_POST['submit'])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    if (empty($email)) {
        $errors['email'] = "Email is required";
    } else {
        $data['email'] =  $email;
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } else {
        $data['password'] = $password;
    }

    if (empty($errors)) {
      $query = "SELECT * FROM users WHERE email=:email";
        $stmt= $conn->prepare($query);
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$user){
            $errors['email'] = "Invalid email";
        }elseif(!password_verify($password,$user['password'])){
           $errors['password'] = "Invalid password";

        }else{
           $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
             $_SESSION['user_role'] = $user['role'];
            $_SESSION['success'] = "Welcome back, " . $user['name'] . "!";
               if ($user['role'] === 'admin') {
            header("Location: index.php");
            exit;
        } else {
            header("Location: ../FontEnd/index.php");
            exit;
        }
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en" data-theme="corporate">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Clean Blogs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-primary/10 to-secondary/10 min-h-screen flex items-center justify-center p-4">
    <div class="card w-full max-w-md bg-base-100 shadow-2xl">
        <!-- ✅ Success Message -->
        <?php if (isset($_SESSION['success'])) { ?>
            <div role="alert" class="alert alert-success mb-6 shadow-md flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium"><?= $_SESSION['success'] ?? '' ?></span>
            </div>
        <?php }
        unset($_SESSION['success']); ?>
        <div class="card-body">
            <h2 class="card-title text-3xl font-bold justify-center mb-2">Welcome Back</h2>
            <p class="text-center text-base-content/60 mb-6">Sign in to your account</p>

            <form class="space-y-4" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Email Address</span>
                    </label>
                    <input type="email" name="email" placeholder="name@example.com"
                        class="input input-bordered w-full focus:input-primary" />
                    <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?? '' ?></p>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Password</span>
                    </label>
                    <input type="password" name="password" placeholder="••••••••"
                        class="input input-bordered w-full focus:input-primary" />
                    <p class="text-red-500 text-sm mt-1"><?= $errors['password'] ?? '' ?></p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="label cursor-pointer gap-2">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" />
                        <span class="label-text">Remember me</span>
                    </label>
                    <a href="password.html" class="link link-primary text-sm font-medium">Forgot Password?</a>
                </div>

                <button type="submit" name="submit" class="btn btn-primary w-full">
                    Login
                </button>
            </form>

            <div class="divider">OR</div>

            <div class="text-center">
                <p class="text-base-content/60">Don't have an account?
                    <a href="register.php" class="link link-primary font-semibold">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>