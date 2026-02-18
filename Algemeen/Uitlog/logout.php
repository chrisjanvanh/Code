<?php
session_start();
session_destroy();
header("Location: ../Inlog/login.php");
exit;
?>