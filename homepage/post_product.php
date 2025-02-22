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

// Get input data
$data = json_decode(file_get_contents("php://input"), true);
$title = $data['title'];
$review_blog = $data['review_blog'];
$media = $data['media']; // Path to the uploaded media file
$rating = $data['rating'];
$user_id = $data['user_id'];

// Insert product into database
$sql = "INSERT INTO products (title, review_blog, media, rating, user_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssdi", $title, $review_blog, $media, $rating, $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Product posted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>