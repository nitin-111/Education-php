<?php
// unsetting various sessions
session_start();
unset($_SESSION['name']);
unset($_SESSION['user_id']);
unset($_SESSION['fname']);
unset($_SESSION['lname']);
unset($_SESSION['email']);
unset($_SESSION['headline']);
unset($_SESSION['summary']);
header('location:login.php');
return;
?>