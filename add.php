<?php
session_start();
include 'db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if (isset($_POST['add_product'])) {
    $user_id = $_SESSION['user_id'];
    $name = substr(trim($_POST['name']), 0, 255);
    $category_id = intval($_POST['category']);
    $price = floatval($_POST['price']);
    $phone = substr(trim($_POST['phone'] ?? ''), 0, 50);
    $whatsapp = substr(trim($_POST['whatsapp'] ?? ''), 0, 50);
    $facebook = substr(trim($_POST['facebook'] ?? ''), 0, 255);
    $instagram = substr(trim($_POST['instagram'] ?? ''), 0, 255);
    $is_free = isset($_POST['is_free']) ? 1 : 0;

    // Validation
    if (empty($name) || empty($category_id) || empty($phone) || empty($_FILES['image']['name'])) {
        $error = "Please fill in all mandatory fields (Name, Category, Phone, and Image).";
    } else {

        if ($is_free) {
            $price = 0;
        }

        // Handle image upload
        $image = time() . '_' . basename($_FILES['image']['name']);
        $target = "uploads/" . $image;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {

            $stmt = $conn->prepare("INSERT INTO products 
                (user_id, name, category_id, price, is_free, image, phone, whatsapp, facebook, instagram) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "isidisssss",
                $user_id,
                $name,
                $category_id,
                $price,
                $is_free,
                $image,
                $phone,
                $whatsapp,
                $facebook,
                $instagram
            );

            if ($stmt->execute()) {
                // Redirect to dashboard with success message
                header("Location: user.php?added=1");
                exit();
            } else {
                $error = "Database error: " . $stmt->error;
            }

            $stmt->close();

        } else {
            $error = "Failed to upload image.";
        }
    }
}

// Fetch categories
$categories = [];
$result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
<style>
.add-product-wrapper { padding:40px 20px; display:flex; justify-content:center; }
.add-product-container { width:100%; max-width:600px; background:#fff; padding:30px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
.branding h1 { color:#1c3a70; margin-bottom:5px; }
.branding p { color:#555; margin-bottom:20px; }
.form-group { margin-bottom:15px; display:flex; flex-direction:column; }
.form-group label { margin-bottom:5px; font-weight:500; }
.form-group input, .form-group select { padding:8px 10px; border-radius:8px; border:1px solid #ccc; font-size:14px; }
.checkbox-group { flex-direction:row; align-items:center; gap:10px; }
.register-btn { background:#1c3a70; color:#fff; padding:10px 15px; border:none; border-radius:8px; cursor:pointer; font-size:16px; }
.register-btn:hover { background:#163058; }
.success-msg { background:#d4edda; color:#155724; padding:10px 15px; margin-bottom:15px; border-radius:8px; text-align:center; }
.error-msg { background:#f8d7da; color:#721c24; padding:10px 15px; margin-bottom:15px; border-radius:8px; text-align:center; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<section class="add-product-wrapper">
    <div class="add-product-container">

        <div class="branding">
            <h1>EWU Add & Drop</h1>
            <p>Add your product and reach EWU students easily</p>
        </div>

        <?php if(isset($success)) echo "<div class='success-msg'>$success</div>"; ?>
        <?php if(isset($error)) echo "<div class='error-msg'>$error</div>"; ?>

        <form action="" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" maxlength="255" required>
            </div>

            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Price (৳) *</label>
                <input type="number" name="price" step="0.01" required>
            </div>

            <div class="form-group checkbox-group">
                <input type="checkbox" name="is_free" id="is_free" onchange="togglePrice(this)">
                <label for="is_free">Sell for Free</label>
            </div>

            <div class="form-group">
                <label>Product Image *</label>
                <input type="file" name="image" accept="image/*" required>
            </div>

            <div class="form-group">
                <label>Phone *</label>
                <input type="text" name="phone" maxlength="50" required>
            </div>

            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" name="whatsapp" maxlength="50">
            </div>

            <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="facebook" maxlength="255">
            </div>

            <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="instagram" maxlength="255">
            </div>

            <button type="submit" name="add_product" class="register-btn">
                <i class="fa-solid fa-plus"></i> Add Product
            </button>

        </form>
    </div>
</section>

<script>
function togglePrice(checkbox) {
    const priceInput = document.querySelector('input[name="price"]');
    if (checkbox.checked) {
        priceInput.value = 0;
        priceInput.readOnly = true;
    } else {
        priceInput.readOnly = false;
        priceInput.value = '';
    }
}
</script>

</body>
</html>