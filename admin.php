<?php
session_start();
include 'db.php';

// Only admin allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// ================= HANDLE AJAX =================
if(isset($_POST['delete_report'])){
    $report_id = intval($_POST['delete_report']);
    $conn->query("DELETE FROM product_reports WHERE id=$report_id");
    echo json_encode(['success' => true]);
    exit();
}

if(isset($_POST['delete_user_report'])){
    $report_id = intval($_POST['delete_user_report']);
    $conn->query("DELETE FROM user_reports WHERE id=$report_id");
    echo json_encode(['success' => true]);
    exit();
}

// Fetch reported products
$reported_products = $conn->query("
    SELECT pr.*, 
           COALESCE(p.name, '[Deleted Product]') AS product_name, 
           COALESCE(u.name, '[Deleted User]') AS reporter_name
    FROM product_reports pr
    LEFT JOIN products p ON pr.product_id = p.id
    LEFT JOIN users u ON pr.reported_by = u.id
    ORDER BY pr.id DESC
");

// Fetch reported users
$reported_users = $conn->query("
    SELECT ur.*, 
           u1.id AS reported_user_id,
           u1.name AS reported_user_name,
           u2.name AS reporter_name
    FROM user_reports ur
    LEFT JOIN users u1 ON ur.user_id = u1.id
    LEFT JOIN users u2 ON ur.reported_by = u2.id
    ORDER BY ur.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel - EWU Add & Drop</title>
<link rel="stylesheet" href="style.css">
<style>
body { font-family: Arial, sans-serif; background: #f7f8fa; margin: 0; }
.navbar { display: flex; justify-content: space-between; align-items: center; background: #1c3a70; color: #fff; padding: 15px 30px; }
.navbar a { color: #fff; margin-left: 20px; text-decoration: none; font-weight: bold; }
.admin-container { padding: 30px; max-width: 1200px; margin: inherit; }
.admin-actions { margin-bottom: 30px; }
.admin-actions a { display:inline-block; margin-right:10px; padding:10px 15px; background:#1c3a70; color:#fff; border-radius:8px; text-decoration:none; font-weight:bold; }
.admin-actions a:hover { background:#163058; }
h2 { margin-top: 40px; color: #1c3a70; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
th, td { padding: 12px; border: 1px solid #ccc; text-align: left; }
th { background: #e5e7eb; }
tr:nth-child(even) { background: #f9f9f9; }
/* Button styling already present */
.view-btn, .delete-report-btn, .view-profile-btn {
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    display: inline-block;
    margin: 3px 3px 3px 0; /* spacing between buttons */
}

/* Responsive: stack buttons vertically on small screens */
@media (max-width: 768px) {
    #reported-products-table td, #reported-users-table td {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .view-btn, .delete-report-btn, .view-profile-btn {
        width: 100%;  /* take full width */
        text-align: center;
    }
}
.view-btn, .delete-report-btn, .view-profile-btn {
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
}
.view-btn { background: #febd69; color: #000; }
.view-btn:hover { background: #ffc800; }
.delete-report-btn { background: #ff4d4d; color: #fff; cursor:pointer; }
.delete-report-btn:hover { background: #cc0000; }
.view-profile-btn { background: #1c3a70; color: #fff; }
.view-profile-btn:hover { background: #f0f0f0; }
.success { background: #d4edda; color: #155724; padding: 12px; border-radius: 10px; text-align: center; margin: 15px 0; display: none; }
</style>
</head>
<body>

<div class="navbar">
    <div class="logo">Admin Panel</div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="admin-container">

    <div id="success-message" class="success">Action completed successfully!</div>

    <div class="admin-actions">
        <a href="manage_categories.php">Manage Categories</a>
    </div>

    <!-- REPORTED PRODUCTS -->
    <h2>Reported Products</h2>
    <table id="reported-products-table">
        <tr>
            <th>Product</th>
            <th>Reported By</th>
            <th>Reason</th>
            <th>Action</th>
        </tr>
        <?php if ($reported_products->num_rows > 0): ?>
            <?php while($p = $reported_products->fetch_assoc()): ?>
            <tr id="report-row-<?= intval($p['id']) ?>">
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= htmlspecialchars($p['reporter_name']) ?></td>
                <td><?= htmlspecialchars($p['reason']) ?></td>
                <td>
                    <?php if(!empty($p['product_id'])): ?>
                        <a class="view-btn" href="detail.php?id=<?= intval($p['product_id']) ?>">View</a>
                    <?php endif; ?>
                    <button class="delete-report-btn" data-id="<?= intval($p['id']) ?>">Delete Report</button>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">No reported products</td></tr>
        <?php endif; ?>
    </table>

   <!-- REPORTED USERS -->
<h2>Reported Users</h2>
<table id="reported-users-table">
    <tr>
        <th>User</th>
        <th>Reported By</th>
        <th>Reason</th>
        <th>Action</th>
    </tr>
    <?php if ($reported_users && $reported_users->num_rows > 0): ?>
        <?php while($u = $reported_users->fetch_assoc()): ?>
        <tr id="user-report-row-<?= intval($u['id']) ?>">
            <td><?= !empty($u['reported_user_id']) ? htmlspecialchars($u['reported_user_name']) : '[Deleted User]' ?></td>
            <td><?= !empty($u['reporter_name']) ? htmlspecialchars($u['reporter_name']) : '[Deleted Reporter]' ?></td>
            <td><?= htmlspecialchars($u['reason']) ?></td>
            <td>
                <?php if(!empty($u['reported_user_id'])): ?>
                    <a class="view-profile-btn" href="user.php?id=<?= intval($u['reported_user_id']) ?>">View Profile</a>
                <?php else: ?>
                    <span style="color:#999;">[Deleted User]</span>
                <?php endif; ?>
                <button class="delete-report-btn" data-user-report-id="<?= intval($u['id']) ?>">Delete Report</button>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">No reported users</td></tr>
    <?php endif; ?>
</table>
</div>

<script>
// Show success message
function showMessage(msg){
    const message = document.getElementById('success-message');
    message.textContent = msg;
    message.style.display = 'block';
    setTimeout(()=> message.style.display = 'none', 3000);
}

// Delete product report
document.querySelectorAll('#reported-products-table .delete-report-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const reportId = this.dataset.id;
        if(confirm('Delete this report?')){
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'delete_report=' + reportId
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    document.getElementById('report-row-'+reportId).remove();
                    showMessage('Product report deleted successfully!');
                }
            });
        }
    });
});

// Delete user report
document.querySelectorAll('#reported-users-table .delete-report-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        const reportId = this.dataset.userReportId;
        if(confirm('Delete this user report?')){
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'delete_user_report=' + reportId
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    document.getElementById('user-report-row-'+reportId).remove();
                    showMessage('User report deleted successfully!');
                }
            });
        }
    });
});
</script>

</body>
</html>