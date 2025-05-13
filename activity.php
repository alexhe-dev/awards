<?php
// Activity Log Page
// Accessed Through Employee Page
session_start();
require_once 'db.php';

// Access control
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'employee') {
header("Location: index.php");
exit;
}

// User variable
$user = $_SESSION['user'];
// Get activities for this employee
$employee_id = $user['id'];
// Only get the 10 most recent activities
$sql = "SELECT * FROM activity_log WHERE employee_id = ? ORDER BY activity_date DESC LIMIT 10";

if (isset($conn)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

?>

<!-- Corresponding HTML - Activity Log Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Log</title>
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

        .activity-container {
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        th {
            background-color: #eee;
        }

        input[type="submit"] {
            margin-top: 20px;
            padding: 8px 16px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: darkviolet;
        }

        p {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="activity-container">
    <h1>Activity Log for<br><?php echo htmlspecialchars($user['name']); ?></h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Activity Type</th>
                <th>Points</th>
                <th>Info</th>
                <th>Date</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['points']); ?></td>
                    <td><?php echo htmlspecialchars($row['info'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['activity_date']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No activities found.</p>
    <?php endif; ?>

    <form action="employee.php" method="post">
        <input type="submit" value="Back to Employee Page">
    </form>
</div>
</body>
</html>
