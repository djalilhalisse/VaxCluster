<?php
session_start();
session_destroy();
header("Location: doctors_login.php");
exit();
?>
