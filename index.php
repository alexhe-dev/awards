<?php
// Login page
session_start();

// DB conn
require_once 'db.php';

$error_msg = '';

// Handle form submission for userid & password
// Handle POST request & undefined values
if ($_SERVER['REQUEST_METHOD'] === 'POST'&&
    isset($_POST['userid']) && isset($_POST['password'])) {
    // Make sure conn is set
    if (isset($conn)) {
        $userid = $conn->real_escape_string($_POST['userid']);
        $password = $_POST['password'];

        // Check in employees
        $sql = "SELECT * FROM employees WHERE userid=?";
        // Robust query handling
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Check password (plain text)
            // Update to hashing ASAP
            if ($password === $user['password']) {
                $_SESSION['user'] = $user;
                $_SESSION['type'] = 'employee';
                header("Location: employee.php");
                exit;
            }
        }

        // Check in managers
        $sql = "SELECT * FROM managers WHERE userid=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $userid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if($password === $user['password']) {
                $_SESSION['user'] = $user;
                $_SESSION['type'] = 'manager';
                header("Location: manager.php");
                exit;
            }
        }

    }
    // If no match, show an error
    $error_msg = "Login failed. Incorrect UserID or Password.";
}
?>

<!-- Corresponding HTML - Login Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
        }
        .login-container {
            text-align: center;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            margin-top: 15px;
        }
        input[type="text"],
        input[type="password"] {
            margin: 5px 0;
            padding: 8px;
            width: 200px;
        }
        input[type="submit"] {
            margin-top: 10px;
            padding: 8px 16px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: darkviolet;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1>Login</h1>

    <?php if (isset($error_msg)): ?>
        <p style="color:purple;"><?php echo $error_msg; ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <label for="userid">User ID:</label><br>
        <input type="text" id="userid" name="userid" required>
        <br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
</div>
</body>
</html>
