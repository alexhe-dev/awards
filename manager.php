<?php
// Manager page
session_start();

// DB conn
require_once 'db.php';

// Redirect if not logged in or not a manager
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'manager') {
    header("Location: index.php");
    exit;
}

// User var for convenience
$user = $_SESSION['user'];



?>

<!-- Corresponding HTML - Manager Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Page</title>
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

        .manager-container {
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 500px;
        }

        input[type="submit"],
        button {
            margin-top: 10px;
            padding: 8px 16px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: darkviolet;
        }

        h1 {
            margin-bottom: 10px;
        }

        p {
            margin: 5px 0 15px;
        }
    </style>
</head>
<body>
<div class="manager-container">
    <h1>Welcome,<br><?php echo htmlspecialchars($user['name']); ?>!</h1>
    <p>Manager Page</p>
    <p>Your ID: <?php echo htmlspecialchars($user['userid']); ?></p>

    <!-- Buttons -->
    <form action="addPoints.php" method="post">
        <input type="submit" name="add_points" value="Add Employee Points">
    </form>

    <form action="addRemoveEmployees.php" method="post">
        <input type="submit" name="add_remove_employees" value="Add / Remove Employees">
    </form>

    <form action="modifyProducts.php" method="post">
        <input type="submit" name="modify_products_list" value="Modify Products List">
    </form>

    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</div>
</body>
</html>
