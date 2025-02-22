<?php
// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the product ID from the URL
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    die("Product ID is missing.");
}

// Fetch product details from the database
$sql = "SELECT * FROM product WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - <?php echo htmlspecialchars($product['title']); ?></title>
    <link rel="stylesheet" href=".//css/navbar.css">
    <link rel="stylesheet" href=".//post-view.css">
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="index.html">CATAMOG</a>
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
                <i class="fas fa-plus"></i>
            </button>
            <button class="nav-button" id="user-page-button">
                <i class="fas fa-user"></i>
            </button>
            <button class="nav-button" id="cart-button">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
    </nav>

    <!-- Post View Content -->
    <div class="post-view">
        <div class="product-media">
            <img src="<?php echo htmlspecialchars($product['media']); ?>" alt="Product Image">
        </div>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['title']); ?></h1>
            <p><?php echo htmlspecialchars($product['review_blog']); ?></p>
            <div class="rating">Rating: <?php echo htmlspecialchars($product['rating']); ?> ★</div>
            <button class="add-to-cart">Add to Cart</button>
            <button class="favorite">★ Favorite</button>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>
</body>
</html>