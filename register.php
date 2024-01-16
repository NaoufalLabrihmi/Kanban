<?php
require_once 'inc/header.php';
require_once 'App.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $request->post('name');
    $email = $request->post('email');
    $username = $request->post('username');
    $password = password_hash($request->post('password'), PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO developers (name, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $username, $password]);

    // Redirect to login page after registration
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">User Registration</h2>
            <form method="post">
                <!-- Your registration form fields go here -->
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
