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

// Get the search term from the query string
$searchTerm = $_GET['search'] ?? '';

if (empty($searchTerm)) {
    die(json_encode(["status" => "error", "message" => "Search term is required."]));
}

// Fetch posts that match the search term in product_name or title
$sql = "SELECT post.*, product.product_name, product.price, users.username 
        FROM post 
        JOIN product ON post.product_id = product.product_id 
        JOIN users ON post.id = users.id 
        WHERE product.product_name LIKE ? OR post.title LIKE ? 
        ORDER BY post.created_at DESC";
$stmt = $conn->prepare($sql);
$searchTerm = "%$searchTerm%"; // Add wildcards for partial matching
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

echo json_encode(["status" => "success", "posts" => $posts]);

$stmt->close();
$conn->close();
?>