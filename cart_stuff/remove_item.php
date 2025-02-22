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

// Remove the item from the database
$sql = "DELETE FROM cart_items WHERE cart_item_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_item_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error removing item."]);
}

$stmt->close();
$conn->close();
?>