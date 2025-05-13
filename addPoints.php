<?php
// Add Employee Points Page
// Accessed Through Manager Page
session_start();
require_once 'db.php';

// Access control
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'manager') {
    header("Location: index.php");
    exit;
}

$message = "";
$employees = [];
$manager_userid = $_SESSION['user']['userid'];

if (isset($conn)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['employee_id']) && isset($_POST['points'])) {
        $employee_id = intval($_POST['employee_id']);
        $points = intval($_POST['points']);

        // Points should be more than 1 due to HTML parameter anyway but double check
        if ($points > 0) {
            // Log the point-adding activity
            $logStmt = $conn->prepare("INSERT INTO activity_log (employee_id, activity_type, points, info, activity_date) VALUES (?, ?, ?, ?, NOW())");
            $activity_type = "earned";
            $logStmt->bind_param("ssis", $employee_id, $activity_type, $points, $manager_userid);
            $logStmt->execute();
            $logStmt->close();

            $updateStmt = $conn->prepare("UPDATE employees SET initial_balance = initial_balance + ? WHERE id = ?");
            $updateStmt->bind_param("ii", $points, $employee_id);
            $updateStmt->execute();
            $updateStmt->close();

            // Redirect to avoid form resubmission
            header("Location: addPoints.php?success=1");
            exit;
        }
    }

    // Logic for displaying employees
    $result = $conn->query("SELECT id, name, userid FROM employees ORDER BY name ASC");
    if ($result && $result->num_rows > 0) {
        $employees = $result->fetch_all(MYSQLI_ASSOC);
    }
} else {
    $message = "Connection to database failed";
}
?>

<!--Corresponding HTML - Add Points Page-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Points</title>
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

        .add-points-container {
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 90%;
            max-width: 500px;
        }

        form {
            margin-top: 15px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        select,
        input[type="number"] {
            margin-top: 5px;
            padding: 8px;
            width: 100%;
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

        p {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="add-points-container">
    <h1>Add Points to Employee</h1>

    <?php if (isset($_GET['success'])): ?>
        <p style="color: green;">Points successfully logged.</p>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="employee">Select Employee:</label>
        <select name="employee_id" required>
            <option value="">--Choose--</option>
            <?php foreach ($employees as $emp): ?>
                <option value="<?php echo $emp['id']; ?>">
                    <?php echo htmlspecialchars($emp['name']) . " (" . htmlspecialchars($emp['userid']) . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="points">Points to Add:</label>
        <input type="number" name="points" required min="1">

        <input type="submit" value="Add Points">
    </form>

    <form action="manager.php" method="post">
        <input type="submit" value="Back to Manager Page">
    </form>
</div>
</body>
</html>
