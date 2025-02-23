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
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['product_id'];
$productName = $data['product_name'];
$price = $data['price'];
$quantity = $data['quantity'];

if (isset($_SESSION['user_id'])) {
    // Database logic for logged-in users
    $userId = $_SESSION['user_id'];

    // Fetch or create an active cart
    $sql = "SELECT cart_id FROM cart WHERE id = ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Create a new cart
        $sql = "INSERT INTO cart (id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $cartId = $stmt->insert_id;
    } else {
        // Use existing cart
        $cart = $result->fetch_assoc();
        $cartId = $cart['cart_id'];
        $_SESSION['cart_id ']= $cart['cart_id'];
    }

    // Add or update the item in the cart
    $sql = "INSERT INTO cart_items (cart_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = quantity + ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiidi", $cartId, $productId, $quantity, $price, $quantity);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Product added to cart!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }


} else {
    // Session logic for guests
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'product_name' => $productName,
            'price' => $price,
            'quantity' => $quantity
        ];
    }

    echo json_encode(["status" => "success", "message" => "Product added to cart!"]);
}

$conn->close();
?>