<?php 
include 'auth.php'; 
include 'db.php'; 

// 🚫 Block guests
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Categories - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ==================== CATEGORY GRID ==================== */
.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    padding: 20px;
}

.category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.2s, box-shadow 0.2s;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.category-card img {
    width: 100%;
    aspect-ratio: 1 / 1;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}

.category-card h3 {
    margin: 10px 0;
    font-size: 1.1rem;
}

.explore-btn {
    background-color: #1d72b8;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.95rem;
}

.explore-btn:hover {
    background-color: #155d8b;
}
</style>
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header>
    <div class="navbar">
        <div class="logo">EWU Add & Drop</div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            
            <a href="user.php"><i class="fa-solid fa-user"></i> Dashboard</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </div>
</header>

<!-- ================= HERO ================= -->
<section class="categories-hero">
    <h1>Explore Our Product Categories</h1>
    <p>From electronics to hostel essentials, find it all from trusted EWU students!</p>
</section>

<!-- ================= CATEGORY GRID ================= -->
<section class="categories">
    <div class="category-grid">
        <?php
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = $conn->query($sql);

        if($result && $result->num_rows > 0):
            while($row = $result->fetch_assoc()):
                $cat_image = !empty($row['image']) ? 'uploads/' . $row['image'] : 'uploads/default-category.png';
        ?>
            <div class="category-card">
                <img src="<?= $cat_image; ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                <h3><?= htmlspecialchars($row['name']); ?></h3>
                <a href="filter.php?id=<?= $row['id']; ?>">
                    <button class="explore-btn">Explore Products</button>
                </a>
            </div>
        <?php 
            endwhile;
        else:
            echo "<p style='text-align:center; color:red; font-weight:bold;'>No categories found.</p>";
        endif;
        ?>
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