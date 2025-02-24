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

// Get the logged-in user's ID (sender)
$sender_id = $_SESSION['user_id'] ?? null;

// Get the receiver's ID and message content
$receiver_id = $_POST['receiver_id'] ?? null;
$message = $_POST['message'] ?? null;

if (!$sender_id || !$receiver_id || !$message) {
    die("Invalid input data.");
}

// Insert the message into the database
$sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    echo "Message sent successfully!";
} else {
    echo "Error sending message: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>