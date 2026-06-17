<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php"); // <-- Home page par redirect
exit;
?>
