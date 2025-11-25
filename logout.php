<?php
session_start();
session_destroy(); //Menghapus semua data login
header("Location: index.php");
exit;
?>