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

// Decode the JSON data sent from the frontend
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['product_id'];
$productName = $data['product_name'];
$price = $data['price'];
$quantity = $data['quantity'];

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the product is already in the cart
if (isset($_SESSION['cart'][$productId])) {
    // Update the quantity if the product exists
    $_SESSION['cart'][$productId]['quantity'] += $quantity;
} else {
    // Add the product to the cart
    $_SESSION['cart'][$productId] = [
        'product_name' => $productName,
        'price' => $price,
        'quantity' => $quantity
    ];
}

// Return a success response
echo json_encode(['success' => true]);

$conn->close();
?>