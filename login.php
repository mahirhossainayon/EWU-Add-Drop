<?php
session_start();
include 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {

        // ✅ FETCH ROLE ALSO
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {

            $stmt->bind_result($user_id, $user_name, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {

                // ✅ SESSION SET
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['role'] = $role;

                // ✅ REDIRECT BASED ON ROLE
                if ($role === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit;

            } else {
                $errors[] = "Incorrect password.";
            }

        } else {
            $errors[] = "Email not found.";
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
  <title>Login - EWU Add & Drop</title>
  <link rel="stylesheet" href="style.css">
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
</head>
<body>

  <!-- AUTH NAVBAR -->
  <header>
    <div class="auth-navbar">
      <div class="logo">EWU Add & Drop</div>
      <nav class="nav-links">
        <a href="index.php">Home</a>
      </nav>
    </div>
  </header>

  <!-- LOGIN FORM -->
  <main class="register-section">
    <div class="register-container">
      <h2>Login to Your Account</h2>
      <p>Welcome back! Enter your details to continue.</p>

      <!-- Display errors -->
      <?php if (!empty($errors)): ?>
          <div class="errors">
              <ul>
                  <?php foreach ($errors as $error): ?>
                      <li><?php echo htmlspecialchars($error); ?></li>
                  <?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="register-btn">Login</button>
      </form>

      <p class="login-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </main>

  <!-- FOOTER -->
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