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
        <form action="checkout.html" method="POST" enctype="multipart/form-data" id="delivery-form">

            <div class="form-left">
    
                <div class="delivery-details">
                    <h2>Delivery Details</h2>
                    <form id="delivery-form">
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
                            <input type="text" id="address" name="address" placeholder="room - street - locality">
                        </div>
                
                        <!-- City Dropdown -->
                        <div class="form-group">
                            <label for="city">City</label>
                            <select id="city" name="city" required>
                                <option value="">Select City</option>
                                <option value="mumbai">Mumbai</option>
                                <option value="delhi">Delhi</option>
                                <option value="bangalore">Bangalore</option>
                                <option value="kolkata">Kolkata</option>
                            </select>
                        </div>
                
                        <!-- State Dropdown -->
                        <div class="form-group">
                            <label for="state">State</label>
                            <select id="state" name="state" required>
                                <option value="">Select State</option>
                                <option value="maharashtra">Maharashtra</option>
                                <option value="delhi">Delhi</option>
                                <option value="karnataka">Karnataka</option>
                                <option value="west-bengal">West Bengal</option>
                            </select>
                        </div>
                
                        <!-- Zip Code -->
                        <div class="form-group">
                            <label for="zip_code">Zip Code</label>
                            <input type="text" id="zip_code" name="zip_code" required>
                        </div>
                
                        <!-- Submit Button -->
                        <button type="submit" class="submit-btn">Submit</button>
                    </form>
                </div>
            </div>
        </form>
    </div>


    <div class="payment-modes" id='payment-block' style="display: none;">
        <div class="form-right">
            <h2>Payment Mode</h2>
            <div class="cart-total">
    <p>Total Amount: ₹<?php echo $cart_total; ?></p>
</div>
            <div class="payment-buttons">
                <!-- Card Payment Button -->
                <button id="card-payment-btn" class="payment-btn">
                    <i class="fas fa-credit-card"></i> Card Payment
                </button>
        
                <!-- UPI/QR Payment Button -->
                <button id="upi-payment-btn" class="payment-btn">
                    <i class="fas fa-qrcode"></i> UPI/QR Payment
                </button>
        
                <!-- Cash on Delivery Button -->
                <button id="cod-btn" class="payment-btn">
                    <i class="fas fa-money-bill-wave"></i> Cash on Delivery
                </button>
            </div>
        
            <!-- Card Payment Form (Hidden by Default) -->
            <div id="card-payment-form" class="card-form" style="display: none;">
                <h3>Card Payment</h3>
                <p>Cards Accepted:</p>
                <div class="card-icons">
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <i class="fab fa-cc-visa"></i>
                    <i class="fas fa-cc-rupay"></i> <!-- BHIM Icon -->
                </div>
        
                <form id="card-details-form">
                    <!-- Name on Card -->
                    <div class="form-group">
                        <label for="card-name">Name on Card</label>
                        <input type="text" id="card-name" name="card-name" required>
                    </div>
        
                    <!-- Card Number -->
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <input type="text" id="card-number" name="card-number" required>
                    </div>
        
                    <!-- Expiration Date -->
                    <div class="form-group">
                        <label for="exp-month">Expiration Date</label>
                        <div class="expiration-date">
                            <input type="text" id="exp-month" name="exp-month" placeholder="MM" required>
                            <input type="text" id="exp-year" name="exp-year" placeholder="YYYY" required>
                        </div>
                    </div>
        
                    <!-- CVV -->
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" required>
                    </div>
        
                    <!-- Proceed Button -->
                    <button type="submit" class="proceed-btn">Proceed to Pay</button>
                </form>
            </div>
        </div> 
    </div>
</div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cityDropdown = document.getElementById('city');
        const stateDropdown = document.getElementById('state');

        // Map cities to their respective states
        const cityStateMap = {
            mumbai: 'maharashtra',
            delhi: 'delhi',
            bangalore: 'karnataka',
            kolkata: 'west-bengal'
        };

        // Add event listener to the city dropdown
        cityDropdown.addEventListener('change', function () {
            const selectedCity = cityDropdown.value;
            if (selectedCity && cityStateMap[selectedCity]) {
                stateDropdown.value = cityStateMap[selectedCity];
            } else {
                stateDropdown.value = '';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const cardPaymentBtn = document.getElementById('card-payment-btn');
        const upiPaymentBtn = document.getElementById('upi-payment-btn');
        const codBtn = document.getElementById('cod-btn');
        const cardPaymentForm = document.getElementById('card-payment-form');

        // Toggle Card Payment Form
        cardPaymentBtn.addEventListener('click', function () {
            cardPaymentForm.style.display = cardPaymentForm.style.display === 'none' ? 'block' : 'none';
        });

        // Handle Card Payment Form Submission
        const cardDetailsForm = document.getElementById('card-details-form');
        cardDetailsForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Get the total amount from the cart (you can fetch this dynamically)
             const totalAmount = parseFloat(document.querySelector('.cart-total').textContent.replace('Total: ₹', '')) * 100; // Convert to paise
            // const totalAmount
            // Razorpay Integration
            const options = {
                key: 'YOUR_RAZORPAY_KEY', // Replace with your Razorpay key
                amount: totalAmount, // Amount in paise
                currency: 'INR',
                name: 'Your Store Name',
                description: 'Payment for Order',
                image: 'https://../images/logo.png', // Replace with your store logo
                handler: function (response) {
                    alert('Payment Successful! Payment ID: ' + response.razorpay_payment_id);
                    // Redirect to order confirmation page or perform other actions
                },
                prefill: {
                    name: document.getElementById('card-name').value,
                    
                },
                theme: {
                    color: '#F37254'
                }
            };

            const rzp = new Razorpay(options);
            rzp.open();

            rzp.on('payment.failed', function (response) {
                alert('Payment failed. Please try again.');
            });

            // handler: function (response) {
            //     window.location.href = 'order_confirmation.php?payment_id=' + response.razorpay_payment_id;
            // }

        });

        // Handle UPI/QR Payment
        upiPaymentBtn.addEventListener('click', function () {
            alert('Redirecting to UPI/QR Payment...');
            // Redirect to UPI/QR payment flow
        });

        // Handle Cash on Delivery
        codBtn.addEventListener('click', function () {
            if (confirm('Are you sure you want to proceed with Cash on Delivery?')) {
                alert('Order confirmed! You will pay upon delivery.');
                // Redirect to order confirmation page
            }
        });
    });
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

            // Send data to the server using fetch
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
                    alert('Delivery details saved successfully!');
                    // Redirect to the payment page or proceed to payment
                    // window.location.href = 'payment.php';
                    const payment=document.getElementById('payment-block');
                    payment.style.display = payment.style.display === 'none' ? 'block' : 'none';
                } else {
                    alert('Failed to save delivery details: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
</script>

</html>