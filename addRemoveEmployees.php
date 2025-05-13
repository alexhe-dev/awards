<?php
// Add & Remove Employees Page
// Accessed Through Manager Page
session_start();
require_once 'db.php';

// Access control
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'manager') {
    header("Location: index.php");
    exit;
}


if (isset($conn)) {

    // Form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Remove employee logic
        if (isset($_POST['remove_employee'])) {
            $employee_id = intval($_POST['employee_id']);
            $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $stmt->close();
        }
        // Removing employee also removes associated activity logs due to cascade

        // Add employee logic
        if (isset($_POST['add_employee'])) {
            $name = trim($_POST['name']);
            $userid = trim($_POST['userid']);
            $password = trim($_POST['password']);
            $initial_balance = intval($_POST['initial_balance']);

            // Execute MySQL query
            if (!empty($name) && !empty($userid) && !empty($_POST['password'])) {
                $stmt = $conn->prepare("INSERT INTO employees (name, userid, password, initial_balance) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $name, $userid, $password, $initial_balance);
                $stmt->execute();
                $stmt->close();
            }
        }
        // Redirect to avoid form resubmission
        header("Location: addRemoveEmployees.php");
        exit;
    }

    // Employee List
    // At the end so that the list will be updated for any form submission
    $employees = [];
    $result = $conn->query("SELECT id, name, userid FROM employees ORDER BY name ASC");
    if ($result && $result->num_rows > 0) {
        $employees = $result->fetch_all(MYSQLI_ASSOC);
    }
}


?>

<!--Corresponding HTML - Managing Employee Page-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add/Remove Employees</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 40px 0;
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
        }

        .container {
            width: 90%;
            max-width: 600px;
            background-color: white;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-top: 15px;
        }

        label {
            display: block;
            margin-top: 10px;
            text-align: left;
        }

        input[type="text"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        input[type="submit"],
        button {
            margin-top: 15px;
            padding: 10px 16px;
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

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            margin: 10px 0;
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 8px;
        }

        li form {
            display: inline;
            margin-left: 10px;
        }

        p {
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Employees</h1>

    <!-- Add Employee Form -->
    <h2>Add Employee</h2>
    <form method="post">
        <label>Name:
            <input type="text" name="name" required>
        </label>

        <label>User ID:
            <input type="text" name="userid" required>
        </label>

        <label>Password:
            <input type="password" name="password" required>
        </label>

        <label>Initial Balance:
            <input type="number" name="initial_balance" min="0" value="0">
        </label>

        <input type="submit" name="add_employee" value="Add Employee">
    </form>

    <!-- Employee List + Removal Button -->
    <h2>Current Employees</h2>
    <?php if (!empty($employees)): ?>
        <ul>
            <?php foreach ($employees as $emp): ?>
                <li>
                    <?php echo htmlspecialchars($emp['name']) . " (" . htmlspecialchars($emp['userid']) . ")"; ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="employee_id" value="<?php echo $emp['id']; ?>">
                        <button type="submit" name="remove_employee" onclick="return confirm('Are you sure?')">Remove</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No employees found.</p>
    <?php endif; ?>

    <form action="manager.php" method="post">
        <input type="submit" value="Back to Manager Page">
    </form>
</div>
</body>
</html>
