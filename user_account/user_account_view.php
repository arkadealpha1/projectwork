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

// Get the user ID from php ID
$user_id = $_GET['id'] ?? null;

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


// Handle favorite user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_user'])) {
    $user_id = $_SESSION['user_id'];
    $favorite_user_id = $_POST['favorite_user_id'];

    $sql = "INSERT INTO favorite_users (user_id, favorite_user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $favorite_user_id);
    $stmt->execute();
    $stmt->close();
}

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
                <a href="../chat_connect/chat.php">
                <i class="fas fa-comment-dots"></i>
                </a>
            </button>
            <button class="nav-button" id="create-post-button">
                <a href="../user_post/user_post.php">
                <i class="fas fa-plus"></i></a>
            </button>
            <button class="nav-button" id="user-page-button">
                <a href="#">
                <!-- <a href="../user_account/user_account.html"> -->
                
                <!-- Fetch logged-in user's profile photo from session -->
                <img class="profile-img" src="<?php echo !empty($_SESSION['profile_photo']) ? $_SESSION['profile_photo'] : '../images/default_profile_pic.jpg'; ?>" alt="profile photo">
                </a>
            </button>
            <button class="nav-button" id="cart-button">
                <a href="../cart_stuff/cart.php">
                <i class="fas fa-shopping-cart"></i>
                </a>
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
        <!-- <button class="favorite-button">★ Favorite</button> -->
        <button class="connect-button" onclick="window.location.href='../chat_connect/chat.php?user2_id=<?php echo $user['id']; ?>'">
            Connect
        </button>
    </div>

    <!-- User Posts Gallery -->
    <div class="user-posts">
        <h2>Posts by <?php echo htmlspecialchars($user['username']); ?></h2>
        <div class="photo-grid">
            <?php foreach ($posts as $post): ?>
                <div class="photo-container">
                        <img src="<?php echo htmlspecialchars($post['media']); ?>" alt="Product Image">
                    <div class="overlay">
                        <a href="../post_view/post_view.php?id=<?php echo $post['post_id']; ?>">
                            <h3 class="product-name"><?php echo htmlspecialchars($post['title']); ?></h3>
                        </a>
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

    <script>
        // Favorite Button
    // document.querySelector('.favorite-button').addEventListener('click', function() {
    //     const favoriteUserId = <?php echo $user['id']; ?>;

    //     fetch('user_account_view.php', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/x-www-form-urlencoded',
    //         },
    //         body: `favorite_user=true&favorite_user_id=${favoriteUserId}`
    //     })
    //     .then(response => response.text())
    //     .then(data => {
    //         alert('Added to favorites!');
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //     });
    // });
        //Connect Button
        document.querySelector('.connect-button').addEventListener('click', function() {
            alert('Connect request sent!');
            // Add your connect logic here
        });
    </script>


</body>
</html>