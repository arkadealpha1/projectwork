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

// Get the ID of the user to connect with (from the URL or form)
$user2_id = $_POST['user2_id'] ?? null;

if (!$user1_id || !$user2_id) {
    die("Invalid user IDs.");
}

// Check if the connection already exists
$sql = "SELECT * FROM connections WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Create a new connection
    $sql = "INSERT INTO connections (user1_id, user2_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user1_id, $user2_id);

    if ($stmt->execute()) {
        echo "Connection created successfully!";
    } else {
        echo "Error creating connection: " . $stmt->error;
    }
} else {
    echo "Connection already exists.";
}

$stmt->close();
$conn->close();
?>