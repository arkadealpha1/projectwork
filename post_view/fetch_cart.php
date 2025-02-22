<?php
session_start(); // Start the session to access user ID

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch the user's active cart
$sql = "SELECT cart_id FROM cart WHERE id = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No active cart found.");
}

$cart = $result->fetch_assoc();
$cart_id = $cart['cart_id'];

// Fetch the cart items
$sql = "SELECT cart_items.*, product.product_name, product.media 
        FROM cart_items 
        JOIN product ON cart_items.product_id = product.product_id 
        WHERE cart_items.cart_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$stmt->close();
$conn->close();

// Return the cart items as JSON
echo json_encode(["status" => "success", "cart_items" => $cart_items]);
?>