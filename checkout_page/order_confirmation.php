<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest order for the user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// Fetch order items
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order['order_id']);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../cart_stuff/cart.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <!-- Navbar content (same as checkout.php) -->
    </nav>

    <!-- Order Confirmation Content -->
    <div class="order-confirmation-container">
        <h1>Order Confirmation</h1>
        <p>Thank you for your order!</p>
        <p>Order ID: <?php echo $order['order_id']; ?></p>
        <p>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></p>
        <h2>Order Items</h2>
        <ul>
            <?php foreach ($order_items as $item): ?>
                <li><?php echo $item['product_id']; ?> - Quantity: <?php echo $item['quantity']; ?> - Price: ₹<?php echo number_format($item['price'], 2); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>
</body>
</html>