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

// Fetch data from the request
$data = json_decode(file_get_contents('php://input'), true);
$payment_id = $data['razorpay_payment_id'];
$order_id = $data['razorpay_order_id'];
$signature = $data['razorpay_signature'];
$cart_id = $data['cart_id'];
$user_id = $data['user_id'];
$total_amount = $data['total_amount'];

// Insert into orders table
$stmt = $conn->prepare("INSERT INTO orders (id, cart_id, total_amount, status, created_at) VALUES (?, ?, ?, 'completed', NOW())");
$stmt->bind_param("iid", $user_id, $cart_id, $total_amount);
$stmt->execute();
$order_id_db = $stmt->insert_id; // Get the auto-generated order ID
$stmt->close();

// Insert into order_items table
$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, created_at) 
                        SELECT ?, product_id, quantity, price, NOW() 
                        FROM cart_items 
                        WHERE cart_id = ?");
$stmt->bind_param("ii", $order_id_db, $cart_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Return success response
echo json_encode(['status' => 'success']);
?>