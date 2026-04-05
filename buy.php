<?php
include 'auth.php';
include 'db.php';

if(!isset($_GET['id'])){
    die("No product selected");
}

$product_id = intval($_GET['id']);

// Get product info
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Product not found");
}

$product = $result->fetch_assoc();
$stmt->close();

// Prevent buying own product
if($product['user_id'] == $_SESSION['user_id']){
    die("You cannot buy your own product");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buy Product - <?= htmlspecialchars($product['name']); ?></title>

<!-- MODERN CSS -->
<style>
/* ================= GLOBAL ================= */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 450px;
    margin: 60px auto;
    background: #ffffff;
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.container:hover {
    transform: translateY(-3px);
}

h2 {
    text-align: center;
    color: #1c3a70;
    margin-bottom: 25px;
    font-size: 24px;
}

/* FORM ELEMENTS */
label {
    display: block;
    font-weight: 600;
    margin-top: 15px;
    margin-bottom: 6px;
    color: #333;
}

select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background: #fafafa;
    font-size: 14px;
    transition: border 0.3s, box-shadow 0.3s;
}

select:focus {
    outline: none;
    border-color: #1c3a70;
    box-shadow: 0 0 5px rgba(28,58,112,0.3);
}

/* BUTTON */
button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: #1c3a70;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    background: #163058;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* RESPONSIVE */
@media(max-width: 500px){
    .container {
        margin: 30px 15px;
        padding: 25px 20px;
    }
    h2 {
        font-size: 20px;
    }
    select, button {
        padding: 10px;
        font-size: 14px;
    }
}
</style>

</head>
<body>

<div class="container">
    <h2>Buy: <?= htmlspecialchars($product['name']); ?></h2>

    <form method="POST" action="place_order.php">

        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">

        <label>Payment Method</label>
        <select name="payment" required>
            <option value="">Select</option>
            <option value="bkash">bKash</option>
            <option value="nagad">Nagad</option>
            <option value="cod">Cash on Delivery</option>
        </select>

        <label>Meeting Place</label>
        <select name="meeting" required>
            <option value="">Select</option>
            <option value="EWU Ground">EWU Ground</option>
            <option value="EWU Cafe">EWU Cafe</option>
            <option value="Front Gate">Front Gate</option>
            <option value="Old Cafe">Old Cafe</option>
        </select>

        <button type="submit">Confirm Purchase</button>

    </form>
</div>

</body>
</html>