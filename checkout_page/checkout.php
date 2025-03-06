<?php
session_start(); // Start the session

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in. Please <a href='login.php'>login</a>.");
}

// Get the user ID and cart ID from the session
$user_id = $_SESSION['user_id'];
$cart_id = $_SESSION['cart_id'] ?? null;

// Check if cart exists
if (!$cart_id) {
    die("Cart not found. Please add items to your cart.");
}

// Fetch cart total amount
$sql = "SELECT SUM(product.price * cart_items.quantity) AS total_amount 
        FROM cart_items 
        JOIN product ON cart_items.product_id = product.product_id 
        WHERE cart_items.cart_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_total = $result->fetch_assoc()['total_amount'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - Checkout</title>
    <link rel="stylesheet" href="../checkout_page/checkout.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../homepage/background.css">

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            <a href="../homepage/homepage.php"><img src="../images/logo.png"></a>
        </div>
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search for products...">
            <button id="search-button"><i class="fas fa-search"></i></button>
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

    <h1>Checkout</h1>
    <p>Total Amount: ₹<?php echo number_format($cart_total + 100, 2); ?></p>

    <!-- Checkout Page Content -->
    <div class="checkout-container">
    <form method="POST" action="checkout.php">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="state">State:</label>
                <input type="text" id="state" name="state" required>
            </div>
            <div class="form-group">
                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" required>
            </div>
            <button type="submit" class="submit-btn">Submit Delivery Details</button>
        </form>

        <button id="rzp-button">Pay Now</button>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>

    <script>
        // Handle delivery form submission
        document.getElementById('deliveryForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the form from reloading the page

            // Collect form data
            const formData = new FormData(this);

            // Send form data to the server using fetch
            fetch('save_delivery_details.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Delivery details saved successfully!');
                } else {
                    alert('Error saving delivery details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        document.getElementById('rzp-button').onclick = function(e) {
            e.preventDefault();

            // Fetch order details from your server
            fetch('create_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ amount: <?php echo $cart_total * 100; ?> }) // Amount in paise
            })
            .then(response => response.json())
            .then(data => {
                var options = {
                    "key": "rzp_test_IQX5JipgAJBxhc", // Replace with your Razorpay Key ID
                    "amount": data.amount, // Amount is in paise
                    "currency": "INR",
                    "name": "CATAMOG",
                    "description": "Order Payment",
                    "image": "https://yourwebsite.com/logo.png", // Replace with your logo
                    "order_id": data.id, // Order ID generated on your server
                    "handler": function (response) {
                        // Handle successful payment
                        fetch('update_order.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature,
                                cart_id: <?php echo $cart_id; ?>,
                                user_id: <?php echo $user_id; ?>,
                                total_amount: <?php echo $cart_total; ?>
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.href = 'order_confirmation.php'; // Redirect to order confirmation page
                            } else {
                                alert('Error updating order details.');
                            }
                        });
                    },
                    "prefill": {
                        "name": "Customer Name", // Replace with customer name
                        "email": "customer@example.com", // Replace with customer email
                        "contact": "9999999999" // Replace with customer phone number
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };
                var rzp = new Razorpay(options);
                rzp.open();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        };
    </script>
</body>
</html>