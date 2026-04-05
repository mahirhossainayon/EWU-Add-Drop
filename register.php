<?php
// register.php
session_start();
include 'db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ================= BASIC VALIDATION =================
    if (empty($fullname)) $errors[] = "Full name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    // ================= EWU EMAIL VALIDATION =================
    if (!empty($email)) {
        $pattern = "/^[0-9]{4}-[0-9]+-[0-9]+-[0-9]+@std\.ewubd\.edu$/";

        if (!preg_match($pattern, $email)) {
            $errors[] = "Only EWU student email allowed! Format: 2023-3-60-101@std.ewubd.edu";
        }
    }

    // ================= CHECK DUPLICATE EMAIL =================
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered.";
        }
        $stmt->close();
    }

    // ================= INSERT USER =================
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $email, $phone, $hashed_password);

        if ($stmt->execute()) {
            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<header>
    <div class="auth-navbar">
        <div class="logo">EWU Add & Drop</div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
        </nav>
    </div>
</header>

<!-- ================= REGISTER FORM ================= -->
<section class="register-section">
    <div class="register-container">
        <h2>Create Your Account</h2>
        <p>Join EWU Add & Drop and start buying & selling items within EWU.</p>

        <!-- Errors -->
        <?php if(!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Success -->
        <?php if($success): ?>
            <div class="success"><?= $success; ?></div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="2023-3-60-101@std.ewubd.edu" required>
                <small style="color:gray;">Use your EWU student email</small>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="Enter your phone number" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter a strong password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="register-btn">Register</button>

            <p class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <p>© 2026 EWU Add & Drop | Built for EWU Students</p>
    <div class="social-icons">
        <i class="fa-brands fa-facebook-f"></i>
        <i class="fa-brands fa-twitter"></i>
        <i class="fa-brands fa-instagram"></i>
    </div>
</footer>

</body>
</html>