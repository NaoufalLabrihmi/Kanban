<?php
require_once 'App.php';

// Start session
$session->start();

// Destroy session and redirect to login page
$session->destroy();
header("Location: login.php");
exit();
?>
