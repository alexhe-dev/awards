<?php
// Employee page
session_start();

require_once 'db.php';

// Redirect if not logged in or not an employee
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'employee') {
    header("Location: index.php");
    exit;
}

// User var for convenience
$user = $_SESSION['user'];

// Get the employee id (PRIMARY KEY)
$employee_id = $user['id'];

$total_points = 0;

// Says initial_balance but will act as total balance for user
// Will update when page is reloaded
if (isset($conn)) {
    $balanceStmt = $conn->prepare("SELECT initial_balance FROM employees WHERE id = ?");
    $balanceStmt->bind_param("i", $employee_id);
    $balanceStmt->execute();
    $balanceStmt->bind_result($total_points);
    $balanceStmt->fetch();
    $balanceStmt->close();
}

// Handle points calculation when button is clicked
// This will be repetitive but is needed
if (isset($conn)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calculate_points'])) {

        // Get total balance for user again
        $balanceStmt = $conn->prepare("SELECT initial_balance FROM employees WHERE id = ?");
        $balanceStmt->bind_param("i", $employee_id);
        $balanceStmt->execute();
        $balanceStmt->bind_result($total_points);
        $balanceStmt->fetch();
        $balanceStmt->close();
    }
}

?>

<!-- Corresponding HTML - Employee Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Page</title>
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
        .employee-container {
            text-align: center;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1 {
            margin-bottom: 10px;
        }
        p {
            margin: 8px 0;
        }
        form {
            margin-top: 10px;
        }
        input[type="submit"],
        button {
            margin-top: 8px;
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
    </style>
</head>
<body>
<div class="employee-container">
    <h1>Welcome,<br><?php echo htmlspecialchars($user['name']); ?>!</h1>
    <p>Your ID: <?php echo htmlspecialchars($user['userid']); ?></p>
    <p>Your current points balance:
        <?php
        if ($total_points === null) {
            echo "Not calculated";
        } else {
            echo htmlspecialchars($total_points);
        }
        ?>
    </p>

    <!-- Button to calculate points -->
    <form action="employee.php" method="post">
        <input type="submit" name="calculate_points" value="Check Current Points Balance">
    </form>

    <!-- Button to check activities -->
    <form action="activity.php" method="post">
        <input type="submit" name="check_activity" value="View Activity Log">
    </form>

    <!-- Button to redeem points -->
    <form action="redeem.php" method="post">
        <input type="submit" name="redeem_points" value="Redeem Points">
    </form>

    <!-- Button to logout -->
    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</div>
</body>
</html>
