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

// Get the user ID from the session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("User ID is missing.");
}

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$stmt->close();

//Fetch posts created by the user

$sql = "SELECT * FROM post WHERE id = ?";
$stmt = $conn->prepare(query: $sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

 $posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../account_view/account_view.css">
    <!-- Add FontAwesome for icons -->
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
                <i class="fas fa-comment-dots"></i>
            </button>
            <button class="nav-button" id="create-post-button">
                <a href="../user_post/user_post.php">
                <i class="fas fa-plus"></i></a>
            </button>
            <button class="nav-button" id="user-page-button">
                <a href="../account_view/account_view.html">
                <!-- <a href="../user_account/user_account.html"> -->
                <img class="profile-img" src="<?php if(!empty($user['Profile_photo'])){ echo $user['Profile_photo'];}else{ echo '../images/default_profile_pic.jpg';} ?>" alt="profile photo" >
                </a>
            </button>
            <button class="nav-button" id="cart-button">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
    </nav>

    <!-- User Profile Section -->
    <div class="user-profile">
        <div class="profile-picture">
            <img src="<?php if(!empty($user['Profile_photo'])){ echo $user['Profile_photo'];}else{ echo '../images/default_profile_pic.jpg';} ?>" alt="profile photo" >
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['username']); ?></h1>
            <p><?php echo htmlspecialchars($user['name']); ?></p>
        </div>
    </div>

    <!-- User Actions -->
    <div class="user-actions">
        <button class="favorite-button">★ Favorite</button>
        <button class="connect-button">Connect</button>
    </div>

    <!-- User Posts Gallery -->
    <div class="user-posts">
        <h2>Posts by <?php echo htmlspecialchars($user['username']); ?></h2>
        <div class="photo-grid">
            <?php foreach ($posts as $post): ?>
                <div class="photo-container">
                    <a href="post_view.php?id=<?php echo $post['id']; ?>">
                        <img src="<?php echo htmlspecialchars($post['media']); ?>" alt="Product Image">
                    </a>
                    <div class="overlay">
                        <h3 class="product-name"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <div class="rating">
                            <span class="stars"><?php echo str_repeat('★', (int)$post['rating']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>
</body>
</html>