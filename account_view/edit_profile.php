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
    die("User not logged in.");
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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - Edit Profile</title>
    <link rel="stylesheet" href="../account_view/edit_profile.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../account_view/account_view.css">
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
                <a href="../account_view/account_view.php">
                <img class="profile-img" src="<?php if(!empty($user['Profile_photo'])){ echo $user['Profile_photo'];}else{ echo '../images/default_profile_pic.jpg';} ?>" alt="profile photo">
                </a>
            </button>
            <button class="nav-button" id="cart-button">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
    </nav>

    <!-- Edit Profile Form -->
    <div class="edit-profile-container">
        <h1>Edit Profile</h1>
        <form id="edit-profile-form" action="update_profile.php" method="POST" enctype="multipart/form-data">
            <!-- Profile Photo -->
            <div class="form-group">
                <label for="profile_photo">Profile Photo</label>
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                <img src="<?php echo !empty($user['Profile_photo']) ? $user['Profile_photo'] : '../images/default_profile_pic.jpg'; ?>" alt="Current Profile Photo" style="width: 100px; height: 100px; border-radius: 50%; margin-top: 10px;">
            </div>

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <!-- Name -->
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <!-- Date of Birth -->
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
            </div>

            <!-- Phone Number -->
            <!-- <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="int" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div> -->

            <!-- Save Button -->
            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>
</body>
</html>