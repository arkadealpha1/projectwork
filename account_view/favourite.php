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

$user_id = $_SESSION['user_id'];

// Fetch favorite posts
$sql = "SELECT post.* FROM post 
        JOIN favorite_posts ON post.post_id = favorite_posts.post_id 
        WHERE favorite_posts.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$favorite_posts = [];
while ($row = $result->fetch_assoc()) {
    $favorite_posts[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Posts</title>
</head>
<body>
    <h1>Favorite Posts</h1>
    <div class="photo-grid">
        <?php foreach ($favorite_posts as $post): ?>
            <div class="photo-container">
                <img src="<?php echo htmlspecialchars($post['media']); ?>" alt="Product Image">
                <div class="overlay">
                    <h3 class="product-name"><?php echo htmlspecialchars($post['title']); ?></h3>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>