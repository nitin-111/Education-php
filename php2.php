<?php
require_once ('server.php');
session_start();
$query = 'SELECT year FROM education WHERE profile_id = :pid';
$stmt = $pdo->prepare($query);
$stmt->execute(array(':pid' => $_SESSION['omid']));
$posi = array();
$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo(json_encode($row));
?>