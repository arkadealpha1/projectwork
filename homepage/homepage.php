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
    <title>CATAMOG - Home</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/photo-grid.css">
    <link rel="stylesheet" href="../homepage/background.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<script src="homepage.js"></script>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="#"><img src="../images/logo.png"></a>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search for products...">
            <button><i class="fas fa-search"></i></button>
        </div>
        <div class="nav-buttons">
            <button class="nav-button" id="chat-button">
                <a href="../chat_connect/chat.php">
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

    <!-- Homepage Posts -->
    <div class="homepage-posts">
        <div class="photo-grid">
            <?php foreach ($posts as $post): ?>
                <div class="photo-container">
                    <!-- <a href="post_view.php?id=<?php echo $post['post_id']; ?>"> -->
                        <!-- Display the uploaded image using the URL from the database -->
                        <img src="<?php echo htmlspecialchars($post['media']); ?>" alt="Product Image">
                    <!-- </a> -->
                    <div class="overlay">
                    <a href="../post_view/post_view.php?id=<?php echo $post['post_id']; ?>">
                        <h3 class="product-name"><?php echo htmlspecialchars($post['title']); ?></h3>
                        </a>
                        <div class="rating">
                            <span class="stars"><?php echo str_repeat('★', (int)$post['rating']); ?></span>
                        </div>
                        <div class="post-details">
                            <p>Posted by: <?php echo htmlspecialchars($post['username']); ?></p>
                            <p>Price: ₹<?php echo htmlspecialchars($post['price']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    
</body>
</html>