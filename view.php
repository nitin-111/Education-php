<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            margin:80px;
        }
    </style>
    <link rel = "stylesheet" href = "css/bootstrap.css">
    <link rel = "stylesheet" href = "css/style.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View</title>
</head>
<body>
<div class = "mx-3">
<?php
// loading some files
require_once ('server.php');
session_start();
if(!isset($_GET['id']))
{
    $_SESSION['error'] = "Id not found";
    header('location:index.php');
    return;
}
    $query = 'SELECT first_name FROM profile WHERE profile_id = :id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':id'=>$_GET['id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row['first_name'] == "")
    {
        $_SESSION['error'] = "Invalid Profile Id";
        header('location:index.php');
        return;
    }
?>
<p style = "font-size:40px; margin-left:40px; margin-top:20px;">Profile information</p>
<?php
$q = 'SELECT * FROM profile WHERE profile_id = :pid';
$s = $pdo->prepare($q);
$s->execute(array(':pid'=>$_GET['id']));
$r = $s->fetch(PDO::FETCH_ASSOC);
echo '<p"><b>First Name: </b>';
echo $r['first_name']; 
echo "</p>";
echo '<p"><b>Last Name: </b>';
echo $r['last_name'];
echo "</p>";
echo '<p><b>Email: </b>';
echo $r['email']; 
echo "</p>";
echo '<p><b>Headline: </b>';
echo $r['headline']; 
echo "</p>";
echo '<p><b>Summary: </b>';
echo $r['summary'];
echo "</p>";
$p = 'SELECT * FROM position WHERE profile_id = :id';
$s = $pdo->prepare($p);
$s->execute(array(':id'=>$_GET['id']));
echo '<p><b>Position: </b>';
echo "</p>";
echo '<ul>';
while($ow = $s->fetch(PDO::FETCH_ASSOC))
{
    echo '<li>';
    echo $ow['year'];
    echo "&nbsp;";
    echo "&nbsp;";
    echo "&nbsp;";
    echo $ow['description'];
    echo "</li>";
}
echo "</ul>";
$query = 'SELECT year, name FROM education JOIN institution ON institution.institution_id = education.institution_id WHERE profile_id = :pid';
$stmt = $pdo->prepare($query);
$stmt->execute(array(':pid'=>$_GET['id']));
echo '<p><b>Education: </b>';
echo '<ul>';
while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    echo '<li>';
    echo $row['year'];
    echo "&nbsp;";
    echo "&nbsp;";
    echo $row['name'];
    echo "</li>";
}
echo "</ul>";
?>
<br>
<a href = "index.php">Done</a>
</body>
</html>
</div>