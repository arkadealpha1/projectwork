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

// Get the post ID from the URL
$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    die("Post ID is missing.");
}

// Fetch the post details from the database
$sql = "SELECT post.*, product.product_name, product.review_blog, product.price, users.username, users.Profile_photo
        FROM post 
        JOIN product ON post.product_id = product.product_id 
        JOIN users ON post.id = users.id 
        WHERE post.post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Post not found.");
}

$post = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - <?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../post_view/post_view.css">
   
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
                <i class="fas fa-plus"></i>
                </a>
            </button>
            <button class="nav-button" id="user-page-button">
                <a href="../account_view/account_view.php">
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

    <!-- Post View Content -->
    <div class="post-view-container">
        <div class="post-left">
            <!-- Display the uploaded image -->
            <img src="<?php echo htmlspecialchars($post['media']); ?>" alt="Product Image">
            
        </div>
        <div class="post-right">
            <!-- Post Details -->
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <p class="product-name"><?php echo htmlspecialchars($post['product_name']); ?></p>
            <p class="price">Price: ₹<?php echo htmlspecialchars($post['price']); ?></p>
            <p class="review-blog"><?php echo htmlspecialchars($post['review_blog']); ?></p>

            <!-- Buttons -->
            <div class="post-buttons">
            <button class="connect-button" onclick="window.location.href='../chat_connect/chat.php?user2_id=<?php echo $post['id']; ?>'">
                Connect
            </button>
            <button class="add-to-cart-button" 
            data-product-id="<?php echo htmlspecialchars($post['product_id']); ?>" 
            data-product-name="<?php echo htmlspecialchars($post['product_name']); ?>" 
            data-price="<?php echo htmlspecialchars($post['price']); ?>">
                Add to Cart
            </button>
            </div>

            <!-- Display the user who uploaded the post -->
             <div class="uploaded-by">
                <a href="../user_account/user_account_view.php?id=<?php echo $post['id']; ?>">
                <img src="<?php echo !empty($post['Profile_photo']) ? $post['Profile_photo'] : '../images/default_profile_pic.jpg'; ?>" alt="profile photo" style="width: 30px; border-radius: 20px ">
                    <span class="username"><?php echo htmlspecialchars($post['username']); ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>

    <script>
    // Connect Button
    document.querySelector('.connect-button').addEventListener('click', function() {
        // alert('Connect request sent!');
        // Add your connect logic here
    });


    // Add to Cart Button
    document.querySelector('.add-to-cart-button').addEventListener('click', function () {
        const productId = this.getAttribute('data-product-id');
        const productName = this.getAttribute('data-product-name');
        const price = this.getAttribute('data-price');
        const quantity = 1; // Default quantity
     fetch('add_tocart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                product_name: productName,
                price: price,
                quantity: quantity
                // product_id: 1,
                // product_name: 'sdf',
                // price: 120,
                // quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status=='success') {
                alert('Product added to cart!');
                // Optionally, update the cart icon count
                updateCartCount();
            } else {
                alert('Failed to add product to cart.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Function to update the cart icon count
    function updateCartCount() {
        fetch('fetch_cart.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_items.length;
                    }
                }
            });
    }
</script>
</body>
</html>