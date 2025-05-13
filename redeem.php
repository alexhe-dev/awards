<?php
// Redeem Products Page
// Accessed Through Employee Page
session_start();
require_once 'db.php';

// Access control
if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'employee') {
    header("Location: index.php");
    exit;
}

$message = "";

// Sets message if redemption is successful
if (isset($_GET['success'])) {
    $product_name = htmlspecialchars($_GET['success']);
    $message = "Successfully redeemed '$product_name'.";
}

// Current user's id
$employee_id = $_SESSION['user']['id'];
// Current user's total points
// Will update when page is reloaded
if (isset($conn)) {
    $balanceStmt = $conn->prepare("SELECT initial_balance FROM employees WHERE id = ?");
    $balanceStmt->bind_param("i", $employee_id);
    $balanceStmt->execute();
    $balanceStmt->bind_result($employee_tot_points);
    $balanceStmt->fetch();
    $balanceStmt->close();
}

if (isset($conn)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);

        // Fetch product details
        $productStmt = $conn->prepare("SELECT product_name, points_required, quantity FROM products WHERE id = ?");
        $productStmt->bind_param("i", $product_id);
        $productStmt->execute();
        $productStmt->bind_result($product_name, $points_required, $quantity);
        $productStmt->fetch();
        $productStmt->close();

        if ($quantity > 0) {


            if ($employee_tot_points >= $points_required) {
                // Deduct product quantity
                $updateStmt = $conn->prepare("UPDATE products SET quantity = quantity - 1 WHERE id = ?");
                $updateStmt->bind_param("i", $product_id);
                $updateStmt->execute();
                $updateStmt->close();

                // Log redemption
                $logStmt = $conn->prepare("INSERT INTO activity_log (employee_id, activity_type, points, info, activity_date) VALUES (?, 'redeemed', ?, ?, NOW())");
                $logStmt->bind_param("iis", $employee_id, $points_required, $product_name);
                $logStmt->execute();
                $logStmt->close();

                // Subtract from user total points
                $updateStmt = $conn->prepare("UPDATE employees SET initial_balance = initial_balance - ? WHERE id = ?");
                $updateStmt->bind_param("ii", $points_required, $employee_id);
                $updateStmt->execute();
                $updateStmt->close();

                // Ensures user doesn't accidentally redeem multiple times by refreshing
                // Passes product name through success
                header("Location: redeem.php?success=" . urlencode($product_name));
                exit;
            } else {
                $message = "You do not have enough points to redeem this item.";
            }
        } else {
            $message = "This product is currently out of stock.";
        }
    }

    // Get list of redeemable products
    $products = [];
    $result = $conn->query("SELECT id, product_name, points_required, quantity FROM products ORDER BY product_name ASC");
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    }
} else {
    $message = "Could not connect to database.";
}
?>

<!--Corresponding HTML - Redeem Page-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redeem Product</title>
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

        .redeem-container {
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        select, input[type="submit"] {
            margin-top: 10px;
            padding: 8px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: purple;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: darkviolet;
        }

        h1, h2 {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
        }

        p {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="redeem-container">
    <h1>Redeem a Product!</h1>

    <?php if (!empty($message)): ?>
        <p style="color: <?= strpos($message, 'Successfully') !== false ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <h2>Your Current Points: <?= $employee_tot_points ?></h2>

    <form method="post">
        <label for="product">Choose a product to redeem:</label>
        <select name="product_id" required>
            <option value="">--Select--</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?= $prod['id'] ?>">
                    <?= htmlspecialchars($prod['product_name']) ?> -
                    <?= $prod['points_required'] ?> points (<?= $prod['quantity'] ?> left)
                </option>
            <?php endforeach; ?>
        </select><br>

        <input type="submit" value="Redeem">
    </form>

    <form action="employee.php" method="post">
        <input type="submit" value="Back to Employee Page">
    </form>
</div>
</body>
</html>