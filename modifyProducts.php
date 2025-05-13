<?php
// Products Page
// Accessed Through Manager Page
session_start();
require_once 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['type'] !== 'manager') {
    header("Location: index.php");
    exit;
}

$message = "";
$products = [];

if(isset($conn)) {

    // Add Product logic
    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $points_required = intval($_POST['points_required']);
        $quantity = intval($_POST['quantity']);

        if (!empty($product_name) && $points_required > 0 && $quantity >= 0) {
            $stmt = $conn->prepare("INSERT INTO products (product_name, points_required, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $product_name, $points_required, $quantity);
            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                $message = "Error adding product.";
            }
            $stmt->close();
        } else {
            $message = "Please provide valid product details.";
        }
    }

    // Update Product logic
    if (isset($_POST['update_product'])) {
        $product_id = intval($_POST['product_id']);
        $new_quantity = intval($_POST['new_quantity']);

        if ($product_id > 0 && $new_quantity >= 0) {
            $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_quantity, $product_id);
            if ($stmt->execute()) {
                $message = "Product updated successfully!";
            } else {
                $message = "Error updating product.";
            }
            $stmt->close();
        } else {
            $message = "Please provide a valid product ID and quantity.";
        }
    }

    // Delete Product logic
    if (isset($_POST['delete_product'])) {
        $product_id = intval($_POST['product_id']);

        if ($product_id > 0) {
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            if ($stmt->execute()) {
                $message = "Product deleted successfully!";
            } else {
                $message = "Error deleting product.";
            }
            $stmt->close();
        } else {
            $message = "Please provide a valid product ID.";
        }
    }

    // Retrieve all products for display
    $result = $conn->query("SELECT id, product_name, points_required, quantity FROM products ORDER BY product_name ASC");
    if ($result && $result->num_rows > 0) {
        $products = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $message = "No products found.";
    }
}
?>

<!--Corresponding HTML - Products Page-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Products</title>
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

        .product-container {
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
            margin-top: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            text-align: left;
        }

        input[type="text"],
        input[type="number"],
        select {
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

        p {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="product-container">
    <h1>Modify Products</h1>

    <?php if (!empty($message)): ?>
        <p style="color: <?= strpos($message, 'uccessfully') !== false ? 'green' : 'red' ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <!-- Add Product Form -->
    <h2>Add New Product</h2>
    <form method="post">
        <label for="product_name">Product Name:</label>
        <input type="text" name="product_name" required>

        <label for="points_required">Points Required:</label>
        <input type="number" name="points_required" required min="1">

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" required min="0">

        <input type="submit" name="add_product" value="Add Product">
    </form>

    <!-- Update Product Form -->
    <h2>Update Product Quantity</h2>
    <form method="post">
        <label for="product_id">Select Product to Update:</label>
        <select name="product_id" required>
            <option value="">--Choose--</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>">
                    <?php echo htmlspecialchars($product['product_name']) . " (Q: " . $product['quantity'] . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="new_quantity">New Quantity:</label>
        <input type="number" name="new_quantity" required min="0">

        <input type="submit" name="update_product" value="Update Quantity">
    </form>

    <!-- Delete Product Form -->
    <h2>Delete Product</h2>
    <form method="post">
        <label for="product_id">Select Product to Delete:</label>
        <select name="product_id" required>
            <option value="">--Choose--</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>">
                    <?php echo htmlspecialchars($product['product_name']) . " (Q: " . $product['quantity'] . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" name="delete_product" value="Delete Product">
    </form>

    <!-- Back to Manager Page -->
    <form action="manager.php" method="post">
        <input type="submit" value="Back to Manager Page">
    </form>
</div>
</body>
</html>
