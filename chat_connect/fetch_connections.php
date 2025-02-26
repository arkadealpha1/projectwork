<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("User not logged in.");
}

// Fetch connected users
$sql = "SELECT u.id, u.username 
        FROM connections c 
        JOIN users u ON (c.user1_id = u.id OR c.user2_id = u.id) 
        WHERE (c.user1_id = ? OR c.user2_id = ?) AND u.id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$connections = [];
while ($row = $result->fetch_assoc()) {
    $connections[] = $row;
}

echo json_encode(["status" => "success", "connections" => $connections]);

$stmt->close();
$conn->close();
?>