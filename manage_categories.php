<?php
include 'auth.php';
include 'db.php';

// Only admin
if($_SESSION['role'] !== 'admin'){
    die("Access denied!");
}

// ================= ADD CATEGORY =================
if(isset($_POST['add_category'])){
    $name = trim($_POST['name']);
    if(empty($name)){
        header("Location: manage_categories.php?error=1");
        exit();
    }

    $image = '';
    if(!empty($_FILES['image']['name'])){
        $image = time().'_'.basename($_FILES['image']['name']);
        if(!move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image)){
            $image = ''; // fallback if upload failed
        }
    }

    $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $image);
    $stmt->execute();

    header("Location: manage_categories.php?added=1");
    exit();
}

// ================= DELETE CATEGORY =================
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT image FROM categories WHERE id=$id");
    $old = $res->fetch_assoc();
    if(!empty($old['image']) && file_exists("uploads/".$old['image'])){
        unlink("uploads/".$old['image']);
    }
    $conn->query("DELETE FROM categories WHERE id=$id");
    header("Location: manage_categories.php?deleted=1");
    exit();
}

// ================= UPDATE CATEGORY =================
if(isset($_POST['update_category'])){
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);

    $res = $conn->query("SELECT name, image FROM categories WHERE id=$id");
    $old = $res->fetch_assoc();

    $image = $old['image']; // keep old image by default

    if(!empty($_FILES['image']['name'])){
        if(!empty($old['image']) && file_exists("uploads/".$old['image'])){
            unlink("uploads/".$old['image']);
        }
        $image = time().'_'.basename($_FILES['image']['name']);
        if(!move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image)){
            $image = $old['image']; // upload failed, keep old
        }
    }

    $stmt = $conn->prepare("UPDATE categories SET name=?, image=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $image, $id);
    $stmt->execute();

    header("Location: manage_categories.php?updated=1");
    exit();
}

// ================= FETCH CATEGORIES =================
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Categories</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    margin:0;
    font-family:Arial, sans-serif;
    background:#f5f6fa;
    color:#333;
}
/* Navbar */
.navbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 30px;
    background:#1c3a70;
    color:#fff;
}
.back-btn {
    display:inline-block;
    padding:6px 12px;
    background:#fff;
    color:#1c3a70;
    border-radius:6px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}
.back-btn:hover { background:#163058; color:#fff; }

/* Container */
.dashboard-container {
    display:flex;
    gap:30px;
    padding:40px 20px;
    max-width:1200px;
    margin:auto;
    flex-wrap:wrap;
}

/* Add Category Card */
.left-panel {
    flex:1 1 280px;
    background:#fff;
    padding:25px;
    border-radius:16px;
    box-shadow:0 8px 25px rgba(0,0,0,0.05);
}
.left-panel h2 {
    color:#1c3a70;
    margin-bottom:20px;
    font-size:20px;
}
.left-panel input[type=text], .left-panel input[type=file] {
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
    box-sizing:border-box;
}
.left-panel button {
    width:100%;
    padding:10px;
    border:none;
    border-radius:8px;
    background:#1c3a70;
    color:#fff;
    cursor:pointer;
    font-weight:600;
    transition:0.3s;
}
.left-panel button:hover { background:#163058; }

/* Categories Grid */
.right-panel {
    flex:3 1 700px;
}
.products-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));
    gap:25px;
}

/* Card Style */
.card {
    background:#fff;
    border-radius:16px;
    padding:15px;
    text-align:center;
    display:flex;
    flex-direction:column;
    align-items:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
    transition:0.3s;
}
.card:hover { transform:translateY(-5px); }
.card img {
    width:100%;
    aspect-ratio:1/1; /* square image */
    object-fit:cover;
    border-radius:12px;
    margin-bottom:10px;
}
.card h3 {
    margin:10px 0;
    font-size:16px;
    color:#1c3a70;
}

/* Buttons in card */
.card-buttons {
    display:flex;
    gap:5px;
    width:100%;
    margin-top:auto;
}
.card-buttons button, .card-buttons a {
    flex:1;
    padding:6px 0;
    font-size:13px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    text-decoration:none;
    color:#fff;
    transition:0.3s;
}
.update-btn { background:#1c3a70; }
.update-btn:hover { background:#163058; }
.delete-btn { background:#ff4d4d; text-align:center; display:inline-block; }
.delete-btn:hover { background:#cc0000; }

/* Inline Edit Form */
.inline-edit {
    display:none;
    flex-direction:column;
    gap:6px;
    margin-top:10px;
    width:100%;
}
.inline-edit input[type=text], .inline-edit input[type=file] {
    width:100%;
    padding:6px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:13px;
}
.inline-edit button {
    padding:6px;
    font-size:13px;
    background:#1c3a70;
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
.inline-edit button:hover { background:#163058; }

/* Success Message */
.success {
    background:#d4edda;
    color:#155724;
    padding:12px 20px;
    border-radius:12px;
    text-align:center;
    margin:20px auto;
    max-width:1200px;
    font-weight:600;
    opacity:1;
    transition:opacity 0.5s;
}

@media(max-width:900px){
    .dashboard-container{ flex-direction:column; }
}
</style>
<script>
function toggleEdit(id){
    const form = document.getElementById('edit-'+id);
    form.style.display = (form.style.display==='flex') ? 'none' : 'flex';
}

// Auto-hide success messages after 3 seconds
window.addEventListener('DOMContentLoaded', () => {
    const msgs = document.querySelectorAll('.success');
    msgs.forEach(msg=>{
        setTimeout(()=>{ msg.style.opacity='0'; },3000);
        setTimeout(()=>{ msg.style.display='none'; },3500);
    });
});
</script>
</head>
<body>

<div class="navbar">
    <div>Admin - Categories</div>
    <div><a href="admin.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back</a></div>
</div>

<?php if(isset($_GET['added'])): ?><div class="success">Category Added</div><?php endif; ?>
<?php if(isset($_GET['updated'])): ?><div class="success">Category Updated</div><?php endif; ?>
<?php if(isset($_GET['deleted'])): ?><div class="success">Category Deleted</div><?php endif; ?>

<div class="dashboard-container">
    <!-- ADD CATEGORY -->
    <div class="left-panel">
        <h2>Add New Category</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Category Name" required>
            <input type="file" name="image">
            <button name="add_category"><i class="fa fa-plus"></i> Add</button>
        </form>
    </div>

    <!-- CATEGORY CARDS -->
    <div class="right-panel">
        <h2>All Categories</h2>
        <div class="products-grid">
            <?php foreach($categories as $cat): ?>
                <div class="card">
                    <img src="<?= !empty($cat['image']) && file_exists('uploads/'.$cat['image']) ? 'uploads/'.$cat['image'] : 'uploads/default-category.png' ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
                    <h3><?= htmlspecialchars($cat['name']); ?></h3>

                    <div class="card-buttons">
                        <button class="update-btn" onclick="toggleEdit(<?= $cat['id']; ?>)"><i class="fa fa-pen"></i> Edit</button>
                        <a href="?delete=<?= $cat['id']; ?>" class="delete-btn" onclick="return confirm('Delete this category?')"><i class="fa fa-trash"></i> Delete</a>
                    </div>

                    <form id="edit-<?= $cat['id']; ?>" class="inline-edit" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $cat['id']; ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($cat['name']); ?>" required>
                        <input type="file" name="image">
                        <button type="submit" name="update_category"><i class="fa fa-check"></i> Save</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>