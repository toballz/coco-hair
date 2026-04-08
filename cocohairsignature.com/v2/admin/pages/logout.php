<?php
include("../config.php");
 
$_SESSION['admin_logged_in']=[];
$_SESSION = [];
 
session_destroy();

header("Location: /v2/admin/pages/login.php"); 
?>
