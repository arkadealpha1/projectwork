<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("User not logged in.");
}

// Fetch all posts with product and user details
$sql = "SELECT post.*, post.title, post.review_blog, product.price, users.username 
        FROM post 
        JOIN product ON post.product_id = product.product_id 
        JOIN users ON post.id = users.id 
        ORDER BY post.created_at DESC"; // Order by newest first
$result = $conn->query($sql);

$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - Chat</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../chat_connect/chat.css">
    <link rel="stylesheet" href="../homepage/background.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="../homepage/homepage.php"><img src="../images/logo.png"></a>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search for products...">
            <button><i class="fas fa-search"></i></button>
        </div>
        <div class="nav-buttons">
            <button class="nav-button" id="chat-button">
                <a href="#">
                <i class="fas fa-comment-dots"></i></a>
            </button>
            <button class="nav-button" id="create-post-button">
                <a href="../user_post/user_post.php">
                <i class="fas fa-plus"></i></a>
            </button>
            <button class="nav-button" id="user-page-button">
                <a href="../account_view/account_view.php">
                <!-- <i class="fas fa-user"></i> -->
                
                <!-- Fetch logged-in user's profile photo from session -->
                <img class="profile-img" src="<?php echo !empty($_SESSION['Profile_photo']) ? $_SESSION['profile_photo'] : '../images/default_profile_pic.jpg'; ?>" alt="profile photo">
                </a>
            </button>
            <button class="nav-button" id="cart-button">
                <a href="../cart_stuff/cart.php">
                <i class="fas fa-shopping-cart"></i>
                </a>
            </button>
        </div>
    </nav>


    <div class="chat-container">
    <!-- Sidebar with connected users -->
        <div class="sidebar">
            <h3>Messages</h3>
            <ul id="connected-users">
            <!-- List of connected users will be populated here -->
            </ul>
        </div>

    <!-- Chat area -->
    <div class="chat-area">
        <div id="chat-messages">
            <!-- Chat messages will be displayed here -->
        </div>
        <div class="message-input">
            <input type="text" id="message-input" placeholder="Type a message...">
            <button id="send-button">Send</button>
        </div>
    </div>
</div>
</body>

<script>
        // JavaScript for chat functionality
        document.addEventListener('DOMContentLoaded', function () {
            const messageInput = document.getElementById('message-input');
            const sendButton = document.getElementById('send-button');
            const chatMessages = document.getElementById('chat-messages');

            // Get the user ID of the user to chat with from the URL
            const urlParams = new URLSearchParams(window.location.search);
            const user2_id = urlParams.get('user2_id');

            // Fetch and display connected users
            fetch('fetch_connections.php')
                .then(response => response.json())
                .then(data => {
                    const connectedUsers = document.getElementById('connected-users');
                    data.connections.forEach(connection => {
                        const li = document.createElement('li');
                        li.textContent = connection.username;
                        li.addEventListener('click', () => {
                            window.location.href = `chat.php?user2_id=${connection.id}`;
                        });
                        connectedUsers.appendChild(li);
                    });
                });

            // Load chat history for the selected user
            function loadChat(user2_id) {
                fetch(`fetch_message.php?user2_id=${user2_id}`)
                    .then(response => response.json())
                    .then(data => {
                        chatMessages.innerHTML = '';
                        data.messages.forEach(message => {
                            const messageDiv = document.createElement('div');
                            messageDiv.className = message.sender_id === <?php echo $_SESSION['user_id']; ?> ? 'message sent' : 'message received';
                            messageDiv.textContent = message.message;
                            chatMessages.appendChild(messageDiv);
                        });
                    });
            }

            // Load chat history for the initial user (from the URL)
            if (user2_id) {
                loadChat(user2_id);
            }

            // Send a message
            sendButton.addEventListener('click', function () {
                const message = messageInput.value;
                if (message.trim() && user2_id) {
                    fetch('send_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            receiver_id: user2_id,
                            message: message
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            messageInput.value = '';
                            loadChat(user2_id); // Reload chat history
                        }
                    });
                }
            });
        });
    </script>

</html>