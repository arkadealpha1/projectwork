<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"];
    $new_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Verify token
    $stmt = $conn->prepare("SELECT email FROM user WHERE reset_token=? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        // Update password and clear token
        $stmt = $conn->prepare("UPDATE user SET password=?, reset_token=NULL, reset_expiry=NULL WHERE email=?");
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        echo "Password updated successfully!";
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/style.css">
    <script defer src="reset_password.js"></script>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <p>Enter a new password for your account.</p>
        <form id="resetPasswordForm">
            <input type="hidden" id="token" name="token">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Update Password</button>
        </form>
        <p id="message"></p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const params = new URLSearchParams(window.location.search);
            document.getElementById("token").value = params.get("token");

            document.getElementById("resetPasswordForm").addEventListener("submit", function(event) {
                event.preventDefault();
                const token = document.getElementById("token").value;
                const password = document.getElementById("password").value;

                fetch("reset_password.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "token=" + encodeURIComponent(token) + "&password=" + encodeURIComponent(password),
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById("message").innerText = data;
                })
                .catch(error => {
                    document.getElementById("message").innerText = "Error processing request.";
                });
            });
        });
    </script>
</body>
</html>
