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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'];
    $product_name = $_POST['product_name'];
    $review_blog = $_POST['review_blog'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in and their ID is stored in the session
    $rating = $_POST['rating'];

    // Handle file upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/'; // Relative path to the uploads folder

        // Check if the uploads directory exists
        if (!is_dir($upload_dir)) {
            // Create the uploads directory if it doesn't exist
            if (!mkdir($upload_dir, 0755, true)) {
                die("Failed to create uploads directory.");
            }
        }

        $file_name = basename($_FILES['media']['name']);
        $file_path = $upload_dir . $file_name; // Absolute server path for saving the file
        $file_url = 'http://localhost/projectwork/uploads/' . $file_name; // Absolute URL for the database
        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['media']['tmp_name'], $file_path)) {
            // Insert product into the `product` table
            $sql = "INSERT INTO product (product_name, review_blog, media, price, rating) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdi", $product_name, $review_blog, $file_url, $price, $rating);

            if ($stmt->execute()) {
                $product_id = $stmt->insert_id; // Get the auto-generated product_id

                // Insert post into the `post` table
                $sql = "INSERT INTO post (product_id, title, media, review_blog, id, rating) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssii", $product_id, $title, $file_url, $review_blog, $user_id, $rating);

                if ($stmt->execute()) {
                    echo "<p>Post created successfully!</p>";
                } else {
                    echo "<p>Error creating post: " . $stmt->error . "</p>";
                }
            } else {
                echo "<p>Error creating product: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Error uploading file.</p>";
        }
    } else {
        echo "<p>No file uploaded or file upload error.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - Create Post</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../user_post/user_post.css">
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
                <a href="../user_post/user_post.html">
                <i class="fas fa-plus"></i></a>
            </button>
            <button class="nav-button" id="user-page-button">
                <a href="../account_view/account_view.php">
                <img class="profile-img" src="<?php if(!empty($user['Profile_photo'])){ echo $user['Profile_photo'];}else{ echo '../images/default_profile_pic.jpg';} ?>" alt="profile photo" >
                </a>
            </button>
            <button class="nav-button" id="cart-button">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
    </nav>

    <!-- Create Post Form -->
    <div class="create-post-container">
        <h1>Create a New Post</h1>
        <form action="user_post.php" method="POST" enctype="multipart/form-data">
            <div class="form-left">
                <!-- Media Upload -->
                <div class="media-upload">
                    <label for="media">Upload Media (Image/Video)</label>
                    <input type="file" id="media" name="media" accept="image/*, video/*" required>
                    <div class="preview-box">
                        <img id="preview" src="#" alt="Media Preview" style="display: none;">
                    </div>
                </div>
            </div>
            <div class="form-right">
                <!-- Post Title -->
                <div class="form-group">
                    <label for="title">Post Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <!-- Product Name -->
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>

                <!-- Product Description -->
                <div class="form-group">
                    <label for="review_blog">Product Description/Review Blog</label>
                    <textarea id="review_blog" name="review_blog" rows="5" required></textarea>
                </div>

                <!-- Product Price -->
                <div class="form-group">
                    <label for="price">Product Price</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>

                <!-- Rating -->
                <div class="form-group">
                    <label for="rating">Rating</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" required>
                </div>

                <!-- Submit Button -->
                <button type="submit">Create Post</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 CATAMOG. All rights reserved.</p>
    </footer>

    <!-- JavaScript for Media Preview -->
    <script>
        const mediaInput = document.getElementById('media');
        const preview = document.getElementById('preview');

        mediaInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>