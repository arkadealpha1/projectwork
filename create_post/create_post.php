<?php
session_start();
include 'db_connect.php'; // Ensure this file contains the database connection setup

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Ensure the user is logged in
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $title = $_POST['title'];
    $review_blog = $_POST['review_blog'];
    
    // Handle file uploads
    $image_path = "uploads/" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    
    $media_path = "uploads/" . basename($_FILES['media']['name']);
    move_uploaded_file($_FILES['media']['tmp_name'], $media_path);
    
    // Insert into product table
    $sql_product = "INSERT INTO product (name, description, image, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_product);
    $stmt->bind_param("sssd", $product_name, $description, $image_path, $price);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();
    
    // Insert into post table
    $sql_post = "INSERT INTO post (product_id, user_id, title, media, review_blog) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_post);
    $stmt->bind_param("iisss", $product_id, $user_id, $title, $media_path, $review_blog);
    $stmt->execute();
    $stmt->close();
    
    header("Location: index.php"); // Redirect to homepage
    exit();
} else {
    echo "Invalid request.";
}
?>
