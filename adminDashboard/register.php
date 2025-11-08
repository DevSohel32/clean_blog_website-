<?php
session_start();
include '../FontEnd/lib/connection.php';
include 'common/validation.php';
$data = [];
$errors = [];

if (isset($_POST['submit'])) {
    $fname = Validation($_POST["fname"]);
    $lname = Validation($_POST["lname"]);
    $email = Validation($_POST["email"]);
    $password = Validation($_POST["password"]);
    $confirm = Validation($_POST["confirm"]);
    $name = $fname . ' ' . $lname;
    // ✅ Validation
    if (empty($fname)) {
        $errors['fname'] = "First name is required";
    } elseif (strlen($fname) < 2) {
        $errors['fname'] = "First name must be at least 2 characters";
    } else {
        $data['fname'] = $data;
    }

    if (empty($lname)) {
        $errors['lname'] = "Last name is required";
    } else {
        $data['lname'] = $data;
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    } else {
        $data['email'] = $data;
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    } else {
        $data['password'] = $data;
    }

    if (empty($confirm)) {
        $errors['confirm'] = "Please confirm your password";
    } elseif ($password !== $confirm) {
        $errors['confirm'] = "Passwords do not match";
    } else {
        $data['confirm'] = $data;
    }
    if (empty($errors)) {
         $role_id = 1;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, password_hash, role_id)
            VALUES (:name, :email, :password_hash, :role_id);";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password_hash', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful!";
            header("Location: login.php");
            exit;
        } else {
            $errors['database'] = "Something went wrong. Please try again.";
        }
    }
}

?>




<!DOCTYPE html>
<html lang="en" data-theme="corporate">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Clean Blogs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-secondary/10 to-accent/10 min-h-screen flex items-center justify-center p-4">
    <div class="card w-full max-w-lg bg-base-100 shadow-2xl">
        <div class="card-body">
            <h2 class="card-title text-3xl font-bold justify-center mb-2">Create Account</h2>
            <p class="text-center text-base-content/60 mb-6">Join us today</p>

            <form class="space-y-4" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">First Name</span>
                        </label>
                        <input type="text" placeholder="John" name="fname"
                            class="input input-bordered w-full focus:input-primary"
                            value="<?= $data['fname'] ?? '' ?>" />
                        <p class="text-red-500 text-sm mt-1"><?= $errors['fname'] ?? '' ?></p>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Last Name</span>
                        </label>
                        <input type="text" placeholder="Doe" name="lname"
                            class="input input-bordered w-full focus:input-primary"
                            value="<?= $data['lname'] ?? '' ?>" />
                        <p class="text-red-500 text-sm mt-1"><?= $errors['lname'] ?? '' ?></p>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Email Address</span>
                    </label>
                    <input type="email" name="email" placeholder="name@example.com"
                        class="input input-bordered w-full focus:input-primary" value="<?= $data['email'] ?? '' ?>" />
                    <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?? '' ?></p>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="••••••••"
                            class="input input-bordered w-full focus:input-primary" />
                        <p class="text-red-500 text-sm mt-1"><?= $errors['password'] ?? '' ?></p>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Confirm Password</span>
                        </label>
                        <input type="password" name="confirm" placeholder="••••••••"
                            class="input input-bordered w-full focus:input-primary" />
                        <p class="text-red-500 text-sm mt-1"><?= $errors['confirm'] ?? '' ?></p>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-2">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-primary" />
                        <span class="label-text">I agree to the Terms & Conditions</span>
                    </label>
                </div>

                <button type="submit" name="submit" class="btn btn-primary w-full">
                    Create Account
                </button>
            </form>

            <div class="divider">OR</div>

            <div class="text-center">
                <p class="text-base-content/60">Already have an account?
                    <a href="login.php" class="link link-primary font-semibold">Login</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>