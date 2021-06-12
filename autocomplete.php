<?php
require_once('server.php');
session_start();
$query = 'SELECT name FROM institution';
$stmt = $pdo->query($query);
$posi = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    $posi[] = $row['name'];
}
echo (json_encode($posi));
?>