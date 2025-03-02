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
$user_id = $_SESSION['user_id'];

// Fetch users the logged-in user has chatted with
$sql = "SELECT u.id, u.username 
        FROM chat_connections c 
        JOIN users u ON (c.user1_id = u.id OR c.user2_id = u.id) 
        WHERE (c.user1_id = ? OR c.user2_id = ?) AND u.id != ? 
        ORDER BY c.last_message_at DESC";
$stmt = $conn->prepare(query: $sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($users);
?>