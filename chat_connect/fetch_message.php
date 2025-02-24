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
$user1_id = $_SESSION['user_id'] ?? null;

// Get the ID of the user to chat with (from the URL or form)
$user2_id = $_GET['user2_id'] ?? null;

if (!$user1_id || !$user2_id) {
    die("Invalid user IDs.");
}

// Fetch messages between the two users
$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode(["status" => "success", "messages" => $messages]);

$stmt->close();
$conn->close();
?>