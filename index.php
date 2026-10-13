<?php
if (!isset($_GET['admin']) || $_GET['admin'] !== 'true') {
    header("Location: /Hospitality-Booking-Platform/front-end/pages/public/home.php"); 
    exit();
}else {
    header("Location: /Hospitality-Booking-Platform/front-end/pages/public/login.php"); 
    exit();
}
?>