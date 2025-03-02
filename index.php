<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATAMOG - Login/Register</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="homepage/background.css">
    
</head>
<body>
    <div class="container">
        <!-- Login Form -->
        <div class="form-container" id="logform">
            <h2>Login</h2>
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="input-group">
                    <label for="login-username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="logform">Login</button>
            </form>
            <p>Don't have an account? <a href="#" id="show-register">Register here</a></p>
            <p>Forgot your password? <a href="forgot_password.php">Reset here</a></p>
        </div>

        <!-- Register Form -->
        <div class="form-container" id="regform" style="display: none;">
            <h2>Register</h2>
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <!-- <div class="input-group">
                    <label for="register-email">Phone</label>
                    <input type="text" id="phone" name="phone" required>
                </div> -->
                <div class="input-group">
                    <label for="register-email">Date of Birth</label>
                    <input type="text" id="dob" name="dob" required>
                </div>
                <div class="input-group">
                    <label for="register-password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
            
                <button type="submit" name="regform">Register</button>
            </form>
            <p>Already have an account? <a href="#" id="show-login">Login here</a></p>
        </div>
    </div>

    <script>
        // Toggle between Login and Register forms
        document.getElementById('show-register').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('logform').style.display = 'none';
            document.getElementById('regform').style.display = 'block';
        });

        document.getElementById('show-login').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('regform').style.display = 'none';
            document.getElementById('logform').style.display = 'block';
        });
    </script>
</body>
</html>