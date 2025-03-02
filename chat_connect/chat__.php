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
$user1_id = $_SESSION['user_id'];

// Get the other user's ID from the URL
$user2_id = $_GET['user2_id'] ?? null;

if (!$user2_id) {
    die("User ID is missing.");
}

// Fetch messages between the two users
$sql = "SELECT * FROM chat_messages 
        WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="../chat_connect/chat.css">
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat with <?php echo htmlspecialchars($user2_id); ?></h2>
        </div>
        <div class="chat-messages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo $message['sender_id'] == $user1_id ? 'sent' : 'received'; ?>">
                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                    <span class="timestamp"><?php echo $message['created_at']; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="chat-input">
            <textarea id="message-input" placeholder="Type your message..."></textarea>
            <button id="send-button">Send</button>
        </div>
    </div>

    <script>
        // Send message functionality
        document.getElementById('send-button').addEventListener('click', function() {
            const message = document.getElementById('message-input').value;
            if (message.trim() === '') return;

            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    sender_id: <?php echo $user1_id; ?>,
                    receiver_id: <?php echo $user2_id; ?>,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload the page to show the new message
                    window.location.reload();
                } else {
                    alert('Failed to send message.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>