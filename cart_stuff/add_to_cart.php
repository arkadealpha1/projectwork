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

// Get the product ID and quantity from the request
// $product_id = $_POST['product_id'];
// $quantity = $_POST['quantity'];
// $id = $_SESSION['user_id']; // Assuming the user is logged in and their ID is stored in the session

// // Fetch the product price
// $sql = "SELECT price FROM product WHERE product_id = ?";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $product_id);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows === 0) {
//     die("Product not found.");
// }

// $product = $result->fetch_assoc();
// $price = $product['price'];

// // Check if the user has an active cart
// $sql = "SELECT cart_id FROM cart WHERE id = ? AND status = 'active'";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows === 0) {
//     // Create a new cart for the user
//     $sql = "INSERT INTO cart (id) VALUES (?)";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $cart_id = $stmt->insert_id;
// } else {
//     // Use the existing active cart
//     $cart = $result->fetch_assoc();
//     $cart_id = $cart['cart_id'];
// }

// // Add the product to the cart
// $sql = "INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("iiid", $cart_id, $product_id, $quantity, $price);

// if ($stmt->execute()) {
//     echo json_encode(["status" => "success", "message" => "Product added to cart!"]);
// } else {
//     echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
// }

// $stmt->close();

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