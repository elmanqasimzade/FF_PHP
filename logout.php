<?php
session_start();
session_destroy(); // Sessiyanı bitir
header("Location: index.php");
exit();
?>
