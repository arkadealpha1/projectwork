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

// Get the user ID from the session
$user_id = $_SESSION['user_id'] ?? null;
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

if (!$user_id) {
    die(json_encode(["status" => "error", "message" => "User not logged in."]));
}

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate the data
if (empty($data['full_name']) || empty($data['email']) || empty($data['address']) || empty($data['city']) || empty($data['state']) || empty($data['zip_code'])) {
    die(json_encode(["status" => "error", "message" => "All fields are required."]));
    // die(json_encode(["status" => "error", "message" => empty($data['zip_code'])]));
}

// Prepare the SQL query
$sql = "INSERT INTO delivery_details (id, full_name, email, address, city, state, zip_code,cart_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssi", $user_id, $data['full_name'], $data['email'], $data['address'], $data['city'], $data['state'], $data['zip_code'],$cart_id);

// Execute the query
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Delivery details saved successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>