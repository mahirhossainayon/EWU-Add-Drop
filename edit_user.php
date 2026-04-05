<?php
session_start();
include 'auth.php'; // Ensure user is logged in
include 'db.php';

$user_id = $_SESSION['user_id'];
$msg = "";

// Fetch current user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");


$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if(!$user){
    die("User not found.");
}

// Handle form submission
if(isset($_POST['update_profile'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Simple validation
    if(empty($name) || empty($email)){
        $msg = "Name and Email are required.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
        if($stmt->execute()){
            $msg = "Profile updated successfully.";
            // Refresh user data
            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
        } else {
            $msg = "Error updating profile. Please try again.";
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
<title>Edit Profile - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
.navbar { display:flex; justify-content:space-between; align-items:center; padding:15px 30px; background:#1c3a70; color:#fff; flex-wrap:wrap; }
.navbar .logo { font-size:24px; font-weight:bold; }
.nav-links a { color:#fff; margin-left:15px; text-decoration:none; font-weight:500; }
.nav-links a:hover { text-decoration:underline; }
.container { max-width:600px; margin:50px auto; padding:20px; background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
h2 { color:#1c3a70; margin-bottom:20px; text-align:center; }
form label { display:block; margin:10px 0 5px; font-weight:bold; }
form input { width:100%; padding:8px; border-radius:8px; border:1px solid #ccc; }
form button { margin-top:20px; padding:10px 20px; background:#1c3a70; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:16px; }
form button:hover { background:#163058; }
.message { margin-top:15px; text-align:center; color:green; }
.error { color:red; }
.nav-links .btn-home { margin-left:0; background:#fff; color:#1c3a70; padding:6px 12px; border-radius:8px; text-decoration:none; font-weight:500; }
.nav-links .btn-home:hover { background:#e0e0e0; color:#163058; }
</style>
</head>
<script>
  // Wait until the page loads
  window.addEventListener('DOMContentLoaded', (event) => {
    const msgDiv = document.querySelector('.message');
    if(msgDiv){
      // Remove the message after 4 seconds (4000ms)
      setTimeout(() => {
        msgDiv.style.display = 'none';
      }, 4000);
    }
  });
</script>
<body>

<header class="navbar">
    <div class="logo">EWU Add & Drop</div>
    <div class="nav-links">
       <a href="user.php"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="index.php" class="btn-home"><i class="fa-solid fa-home"></i> Home</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</header>

<div class="container">
    <h2>Edit Profile</h2>
    <?php if(!empty($msg)): ?>
        <div class="message"><?= htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="name">Name*</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']); ?>" required>

        <label for="email">Email*</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']); ?>" required>

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']); ?>">

        <button type="submit" name="update_profile"><i class="fa-solid fa-save"></i> Update Profile</button>
    </form>
</div>

</body>
</html>