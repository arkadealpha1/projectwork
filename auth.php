<?php
// Enable detailed error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include 'connect.php';

if(isset($_POST['regform'])){
    $username=$_POST['username'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $dob=$_POST['dob'];
    $password=$_POST['password'];
    $password=md5($password);

    $checkEmail="SELECT * From users where email='$email'";
    $result=$conn->query($checkEmail);
    if($result->num_rows>0){
        echo "Email already exists!";
    }

    $checkUsername="SELECT * From users where username='$username'";
    $result=$conn->query($checkUsername);
    if($result->num_rows>0){
        echo "Username already exists!";
    }

    else{
        $insertQuery="INSERT INTO users(username,email,password)
                        VALUES ('$username','$email','$password')";
            if($conn->query($insertQuery)==TRUE){
                header("location: index.php"); 
            } 

            else{
                echo "Error:".$conn->error;
            }
        }

}

if(isset($_POST['logform'])){
    $username=$_POST['username'];
    $password=$_POST['password'];
    $password=md5($password);

    $sql="SELECT * FROM users WHERE username='$username' and password='$password'";
    $result=$conn->query($sql);
    if($result->num_rows>0){
        // echo $result;
        session_start();
        $row=$result->fetch_assoc();
        $_SESSION['user_id']=$row['id'];
        header("Location: homepage/homepage.php");
        exit();

        }
    }
    else{
        echo "Username not found!";
    }


?>
