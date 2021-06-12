<?php
require_once ('server.php');
session_start();
$quer = 'SELECT * FROM position WHERE profile_id = :nid';
$stnt = $pdo->prepare($quer);
$stnt->execute(array(':nid'=>$_SESSION['omid']));
$posi = array();
while ($rows = $stnt->fetch(PDO::FETCH_ASSOC))
{
    $posi[] = $rows['description'];
}
echo(json_encode($posi));
?>
