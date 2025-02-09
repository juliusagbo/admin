<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password

    // Check if username exists
    $checkStmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $message = "Error: Username already exists!";
        $messageClass = "error";
    } else {
        // Insert new admin
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            $message = "Admin registered successfully!";
            $messageClass = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $messageClass = "error";
        }
        $stmt->close();
    }

    $checkStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 300px;
            margin: 50px auto;
            background: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        }
        h2 {
            color: #f1c40f;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: #333;
            color: white;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #f1c40f;
            border: none;
            color: black;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        button:hover {
            opacity: 0.8;
        }
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background: #28a745;
        }
        .error {
            background: #dc3545;
        }
        .login-link {
            display: block;
            text-decoration: none;
            margin-top: 15px;
            padding: 10px;
            background: #f1c40f;
            color: white;
            border-radius: 5px;
            font-weight: bold;
        }
        .login-link:hover {
            background: #f1c40f;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Admin Registration</h2>

    <?php if (isset($message)): ?>
        <p class="message <?= $messageClass; ?>"><?= $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <!-- Login Button -->
    <a href="admin-login.php" class="login-link">Already have an account? Login</a>
</div>

</body>
</html>

