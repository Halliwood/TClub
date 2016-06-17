<?php
session_start();
unset($_SESSION);
session_unset();
session_destroy();
setcookie('tclub_login', '', time() - 3600);
header('Location: login.php');
?>
