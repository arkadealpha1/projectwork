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

// Fetch the latest order for the user
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// Fetch order items
$sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order['order_id']);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch order items with product names
$sql = "
    SELECT oi.*, p.product_name 
    FROM order_items oi
    JOIN product p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order['order_id']);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../homepage/background.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../checkout_page/order_confirmation.css"
</head>
<body>
    <!-- Navbar -->
    <script src="../navbar.js"></script>
    <nav class="navbar">
        <div class="logo">
            <a href="../homepage/homepage.php"><img src="../images/logo.png"></a>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search for products...">
            <button><i class="fas fa-search"></i></button>
        </div>
        <div class="nav-buttons">
            <button class="nav-button" id="chat-button" onclick="openDropdown()">
                <i class="fas fa-comment-dots"></i>
            </button>

            <!--Chat dropdown-->
            <div id="chat-dropdown">
                    <ul id="inbox-list"></ul>
            </div>
            
            <button class="nav-button" id="create-post-button">
                <a href="../user_post/user_post.php">
                <i class="fas fa-plus"></i></a>
            </button>
            <button class="nav-button" id="user-page-button">
                <a href="../account_view/account_view.php">
                <!-- <i class="fas fa-user"></i> -->
                <img class="profile-img" src="<?php if(!empty($user['Profile_photo'])){ echo $user['Profile_photo'];}else{ echo '../images/default_profile_pic.jpg';} ?>" alt="profile photo" >
                </a>
            </button>
            <button class="nav-button" id="cart-button">
                <a href="../cart_stuff/cart.php">
                <i class="fas fa-shopping-cart"></i>
                </a>
            </button>
        </div>
    </nav>

    <!-- Order Confirmation Content -->
    <div class="order-confirmation-container">
        <h1>Thank You for dealing with CATAMOG. Keep on rocking in the free world! 😎💿</h1>
        <h3>Order ID: <?php echo $order['order_id']; ?></h3>
        <h3>Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></h3>
        <h2>Order Items</h2>
        <ul>
            <?php foreach ($order_items as $item): ?>
                <li><?php echo $item['product_id']; ?> - <?php echo htmlspecialchars($item['product_name']); ?> - Price: ₹<?php echo number_format($item['price'], 2); ?></li>
            <?php endforeach; ?>
        </ul>
        <!-- Stamp Message -->
        <div class="stamp-message">See you soon, BigCat! ;)</div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>
</body>
</html>