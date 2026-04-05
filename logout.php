<?php
session_start();

// Remove all session data
session_unset();

// Destroy session
session_destroy();

// Redirect to home
header("Location: index.php");
exit;
?>