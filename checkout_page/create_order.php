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

// Get data from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$cart_id = $data['cart_id'];
$total_amount = $data['total_amount'];

// Insert order into the `orders` table with status "active"
$sql = "INSERT INTO orders (id, cart_id, total_amount, status, created_at) VALUES (?, ?, ?, 'active', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iid", $user_id, $cart_id, $total_amount);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id; // Get the auto-generated order_id

    // Copy cart items to order_items
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, created_at)
            SELECT ?, product_id, quantity, price, NOW() FROM cart_items WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $cart_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "order_id" => $order_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to copy cart items to order_items"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Failed to create order"]);
}

$stmt->close();
$conn->close();
?>