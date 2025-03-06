<?php
include 'connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $token = bin2hex(random_bytes(50));
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Check if email exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Store token in database
        $stmt = $conn->prepare("UPDATE users SET reset_token=?, reset_expiry=? WHERE email=?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send reset link to email
        $reset_link = "http://projectwork/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n\n$reset_link";
        $headers = "From: no-reply@yourwebsite.com";

        mail($email, $subject, $message, $headers);

        echo "A password reset link has been sent to your email.";
    } else {
        echo "Email not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../projectwork/css/style.css">
    <link rel="stylesheet" href="../projectwork/homepage/background.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <p>Enter your email to receive a password reset link.</p>
        <form id="forgotPasswordForm">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p id="message"></p>
    </div>

    <script>
        document.getElementById("forgotPasswordForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const email = document.getElementById("email").value;

            fetch("forgot_password.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "email=" + encodeURIComponent(email),
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("message").innerText = data;
            })
            .catch(error => {
                document.getElementById("message").innerText = "Error processing request.";
            });
        });
    </script>
</body>
</html>

