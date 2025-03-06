<?php
// Enable detailed error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'connect.php';

session_start(); // Start the session at the beginning

if(isset($_POST['regform'])){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $password = md5($password);

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);
    if($result->num_rows > 0){
        echo "Email already exists!";
    }

    $checkUsername = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($checkUsername);
    if($result->num_rows > 0){
        echo "Username already exists!";
    } else {
        $insertQuery = "INSERT INTO users(username, email, password, phone, dob) 
                        VALUES ('$username', '$email', '$password', '$phone', '$dob')";
        if($conn->query($insertQuery) == TRUE){
            header("location: index.php"); 
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if(isset($_POST['logform'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password = md5($password);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['profile_photo'] = $row['Profile_photo'];
        header("Location: homepage/homepage.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Username or password invalid";
        header("Location: index.php"); // Redirect back to the login page
        exit();
    }
}
?>