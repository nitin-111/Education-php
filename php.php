<?php
require_once ('server.php');
session_start();
$quer = 'SELECT year FROM position WHERE profile_id = :nid';
$stnt = $pdo->prepare($quer);
$stnt->execute(array(':nid'=>$_SESSION['omid']));
$row = $stnt->fetchAll(PDO::FETCH_ASSOC);
echo(json_encode($row));
?>
