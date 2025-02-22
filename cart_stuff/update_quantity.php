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

// Get input data
$data = json_decode(file_get_contents("php://input"), true);
$cart_item_id = $data['cart_item_id'];
$quantity = $data['quantity'];

// Update the quantity in the database
$sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $quantity, $cart_item_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error updating quantity."]);
}

$stmt->close();
$conn->close();
?>