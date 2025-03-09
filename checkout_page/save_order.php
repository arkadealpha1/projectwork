<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get data from the session and POST request
$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'];
$total_amount = $_POST['total_amount'];
$payment_id = $_POST['payment_id'];
$status = 'completed'; // Default status for a successful payment

// Insert order into the `orders` table
$sql = "INSERT INTO orders (id, cart_id, total_amount, payment_id, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iidss", $user_id, $cart_id, $total_amount, $payment_id, $status);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id; // Get the auto-generated order_id

    // Fetch cart items to insert into `order_items`
    $sql = "SELECT product_id, quantity, price FROM cart_items WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Insert each cart item into `order_items`
    foreach ($cart_items as $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // Update cart status to 'completed'
    $sql = "UPDATE cart SET status = 'completed' WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();

    echo json_encode(["status" => "success", "order_id" => $order_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to save order details"]);
}

$stmt->close();
$conn->close();
?>