<?php
require_once ('server.php');
session_start();
$quer = 'SELECT name FROM education JOIN institution ON education.institution_id = institution.institution_id WHERE profile_id = :nid';
$stnt = $pdo->prepare($quer);
$stnt->execute(array(':nid'=>$_SESSION['omid']));
$posi = array();
while ($rows = $stnt->fetch(PDO::FETCH_ASSOC))
{
    $posi[] = $rows['name'];
}
echo(json_encode($posi));
?>
