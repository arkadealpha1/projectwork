<?php
session_start(); // Start the session to access user ID

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

// Fetch the user's active cart
$sql = "SELECT cart_id FROM cart WHERE id = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $cart_items = []; // No active cart
} else {
    $cart = $result->fetch_assoc();
    $cart_id = $cart['cart_id'];

    // Fetch the cart items
    $sql = "SELECT cart_items.*, product.product_name, product.media, product.price 
            FROM cart_items 
            JOIN product ON cart_items.product_id = product.product_id 
            WHERE cart_items.cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - Cart</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../cart_stuff/cart.css">
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
                <a href="../account_view/account_view.php">
                <!-- <i class="fas fa-user"></i> -->
                <img class="profile-img" src="<?php if(!empty($user['Profile_photo'])){ echo $user['Profile_photo'];}else{ echo '../images/default_profile_pic.jpg';} ?>" alt="profile photo" >
                </a>
            </button>
            <button class="nav-button" id="cart-button">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
    </nav>

    <!-- Cart Page Content -->
    <div class="cart-container">
        <h1>Your Cart</h1>
        <div class="cart-content">
            <!-- Cart Items -->
            <div class="cart-items">
                <?php if (empty($cart_items)): ?>
                    <p>Your cart is empty.</p>
                <?php else: ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <img src="<?php echo htmlspecialchars($item['media']); ?>" alt="Product Image">
                            </div>
                            <div class="item-details">
                                <h3 class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <p class="item-price">$<?php echo htmlspecialchars($item['price']); ?></p>
                                <!-- <div class="item-quantity">
                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_item_id']; ?>, -1)">-</button>
                                    <input type="number" class="quantity-input" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                                    <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_item_id']; ?>, 1)">+</button>
                                </div> -->
                                <button class="remove-btn" onclick="removeItem(<?php echo $item['cart_item_id']; ?>)">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="summary-details">
                    <p>Subtotal: <span class="subtotal">$<?php echo calculateSubtotal($cart_items); ?></span></p>
                    <p>Shipping: <span class="shipping">$10.00</span></p>
                    <hr>
                    <p>Total: <span class="total">$<?php echo calculateTotal($cart_items); ?></span></p>
                </div>
                <button class="checkout-btn">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>

    <!-- JavaScript for Cart Functionality -->
    <script>
        // Function to update quantity
        function updateQuantity(cartItemId, change) {
            const quantityInput = document.querySelector(`.cart-item[data-id="${cartItemId}"] .quantity-input`);
            let quantity = parseInt(quantityInput.value);
            quantity += change;
            if (quantity < 1) quantity = 1;
            quantityInput.value = quantity;

            // Send an AJAX request to update the quantity in the database
            fetch('update_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ cart_item_id: cartItemId, quantity: quantity }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateTotals();
                } else {
                    alert('Error updating quantity.');
                }
            });
        }

        // Function to remove an item
        function removeItem(cartItemId) {
            // Send an AJAX request to remove the item from the database
            fetch('remove_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ cart_item_id: cartItemId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Remove the item from the DOM
                    location.reload();
                    document.querySelector(`.cart-item[data-id="${cartItemId}"]`).remove();
                    updateTotals();
                } else {
                    alert('Error removing item.');
                }
            });
        }

        // Function to update totals
        function updateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.cart-item').forEach(item => {
                const price = parseFloat(item.querySelector('.item-price').textContent.replace('$', ''));
                // const quantity = parseInt(item.querySelector('.quantity-input').value);
                subtotal += price;
            });

            const shipping = 10.00;
            const total = subtotal + shipping;

            document.querySelector('.subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.querySelector('.total').textContent = `$${total.toFixed(2)}`;
        }

        // Initial call to set totals
        updateTotals();
    </script>
</body>
</html>

<?php
// Helper function to calculate subtotal
function calculateSubtotal($cart_items) {
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'];
    }
    return number_format($subtotal, 2);
}

// Helper function to calculate total
function calculateTotal($cart_items) {
    $subtotal = calculateSubtotal($cart_items);
    $shipping = 10.00;
    return number_format($subtotal + $shipping, 2);
}
?>