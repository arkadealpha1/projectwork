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

// Handle file upload for profile photo
$profile_photo = $_FILES['profile_photo']['name'] ?? null;
if ($profile_photo) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // Create the directory if it doesn't exist
    }

    $file_name = basename($_FILES['profile_photo']['name']);
    $target_file = $target_dir . $file_name;

    // Check if the file is an image
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($image_file_type, $allowed_types)) {
        die("Only JPG, JPEG, PNG, and GIF files are allowed.");
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
        $profile_photo = $target_file; // Save the file path
    } else {
        die("Error uploading file.");
    }

    // $target_file = $target_dir . basename($_FILES['profile_photo']['name']);
    // move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file);
} else {
    $profile_photo = $_POST['current_profile_photo'] ?? null;
}

// Get form data
$username = $_POST['username'];
$name = $_POST['name'];
$date_of_birth = $_POST['date_of_birth'];
$phone = $_POST['phone'];

// Validate phone number (ensure it's a valid integer)
if (!ctype_digit($phone)) {
    die("Invalid phone number. Please enter only digits.");
}

// Update user details in the database
$sql = "UPDATE users SET username = ?, name = ?, date_of_birth = ?, phone = ?, Profile_photo = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $username, $name, $date_of_birth, $phone, $profile_photo, $user_id);

if ($stmt->execute()) {
    echo "Profile updated successfully!";
    // Redirect to the account view page
    header("Location: ../account_view/account_view.php");
    exit();
} else {
    echo "Error updating profile: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>