<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'projectwork';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'] ?? null;
$sql = "SELECT cart_id FROM cart WHERE id = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No active cart found.");
}

$cart = $result->fetch_assoc();
$cart_id = $cart['cart_id'];

if (!$cart_id) {
    die("cart not found.");
}
if (!$user_id) {
    die("User not logged in.");
}
$sql = "SELECT SUM(price * quantity) AS total_amount FROM cart_items WHERE cart_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_total = $result->fetch_assoc()['total_amount'] ?? 0;

$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - Checkout</title>
    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../checkout_page/checkout.css">
    <link rel="stylesheet" href="../homepage/background.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>

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
                <a href="../cart_stuff/cart.php">
                <i class="fas fa-shopping-cart"></i>
                </a>
            </button>
        </div>
    </nav>

<div class="container">
    <div class="checkout-form">
        <form id="delivery-form">
            <div class="delivery-details">
                <h2>Delivery Details</h2>
                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="room - street - locality" required>
                </div>

                <!-- City Dropdown -->
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="Your city" required>
                        <!-- <option value="">Select City</option>
                        <option value="mumbai">Mumbai</option>
                        <option value="delhi">Delhi</option>
                        <option value="bangalore">Bangalore</option>
                        <option value="kolkata">Kolkata</option> -->
                </div>

                <!-- State Dropdown -->
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state" placeholder="Your state" required>
                        <!-- <option value="">Select State</option>
                        <option value="maharashtra">Maharashtra</option>
                        <option value="delhi">Delhi</option>
                        <option value="karnataka">Karnataka</option>
                        <option value="west-bengal">West Bengal</option> -->
                </div>

                <!-- Zip Code -->
                <div class="form-group">
                    <label for="zip_code">Zip Code</label>
                    <input type="text" id="zip_code" name="zip_code" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Proceed to Payment</button>
            </div>
        </form>
    </div>
</div>
</body>

<script>
    // document.addEventListener('DOMContentLoaded', function () {
    //     const cityDropdown = document.getElementById('city');
    //     const stateDropdown = document.getElementById('state');

    //     // Map cities to their respective states
    //     const cityStateMap = {
    //         mumbai: 'maharashtra',
    //         delhi: 'delhi',
    //         bangalore: 'karnataka',
    //         kolkata: 'west-bengal'
    //     };

    //     // Add event listener to the city dropdown
    //     cityDropdown.addEventListener('change', function () {
    //         const selectedCity = cityDropdown.value;
    //         if (selectedCity && cityStateMap[selectedCity]) {
    //             stateDropdown.value = cityStateMap[selectedCity];
    //         } else {
    //             stateDropdown.value = '';
    //         }
    //     });
    // });

    document.addEventListener('DOMContentLoaded', function () {
    const deliveryForm = document.getElementById('delivery-form');

    deliveryForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Get form data
        const formData = new FormData(deliveryForm);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Add user ID from the session
        data.user_id = <?php echo $_SESSION['user_id']; ?>;

        // Send data to the server to save delivery details
        fetch('save_delivery_details.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                // Delivery details saved successfully, now open Razorpay payment gateway
                openRazorpayGateway();
            } else {
                alert('Failed to save delivery details: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    // Function to open Razorpay payment gateway
    function openRazorpayGateway() {
        const totalAmount = <?php echo $cart_total; ?> * 100; // Convert to paise

        const options = {
            key: 'YOUR_RAZORPAY_KEY', // Replace with your Razorpay key
            amount: totalAmount, // Amount in paise
            currency: 'INR',
            name: 'CATAMOG',
            description: 'Payment for Order',
            image: 'https://../images/logo.png', // Replace with your store logo
            handler: function (response) {
                // Handle successful payment
                alert('Payment Successful! Payment ID: ' + response.razorpay_payment_id);

                // Redirect to order confirmation page or perform other actions
                window.location.href = 'order_confirmation.php?payment_id=' + response.razorpay_payment_id;
            },
            prefill: {
                name: document.getElementById('full_name').value,
                email: document.getElementById('email').value,
            },
            theme: {
                color: '#F37254', // Customize the theme color
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();

        rzp.on('payment.failed', function (response) {
            alert('Payment failed. Please try again.');
        });
    }
});
</script>

</html>