<?php
if (!isset($_GET['admin']) || $_GET['admin'] !== 'true') {
    header('Location: front-end/pages/public/home.php', true, 303);
    exit();
}else {
    header('Location: front-end/pages/public/login.php', true, 303);
    exit();
}
?>