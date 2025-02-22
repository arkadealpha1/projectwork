<?php
header("Content-Type: application/json");

// Database connection
$host = 'localhost';
$db = 'catamog';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get user ID from query parameter
$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    die(json_encode(["status" => "error", "message" => "User ID is missing"]));
}

// Fetch products for the user
$sql = "SELECT * FROM products WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode(["status" => "success", "products" => $products]);

$stmt->close();
$conn->close();
?>