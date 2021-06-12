<!DOCTYPE html>
<html lang="en">
<head>
    <link rel = "stylesheet" href = "css/bootstrap.css">
    <link rel = "stylesheet" href = "css/style.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Item</title>
</head>
<body>
<?php
// loading some files
require_once('server.php');
session_start();

// validating user id
if(!isset($_GET['id']))
{
    $_SESSION['error'] = "No user id";
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

// deleting data
if(isset($_POST['submit']) && isset($_POST['del']))
{
    $q = 'DELETE FROM profile WHERE profile_id = :pid';
    $stnt = $pdo->prepare($q);
    $stnt -> execute(array(':pid'=>$_GET['id']));
    $_SESSION['success'] = "Record Deleted";
    $_SESSION['ad']--;
    header('location:index.php');
    return;
}
?>
<p style = "font-size:40px; margin-left:40px; margin-top:20px">Deleting profile</p>
<?php
// displaying user to be deleted
echo '<p style="margin-left:10px"><b>First Name: </b>';
echo $_SESSION['fname'];
echo '<p style="margin-left:10px"><b>First Name: </b>';
echo $_SESSION['lname'];
?>

<!-- form-->
<form method = "POST">
<input type = "hidden" name = "del" id = "del" value = "<?=htmlentities($_GET['id'])?>" />
<input type = "submit" name = "submit" value = "submit" id = "submit" style="display:inline-block" class="btn btn-outline-success col-2" />
<input style="display:inline-block" class="btn btn-outline-danger col-2" type = "button" name = "cancel" value = "Cancel" id = "cancel" onclick = "location.href = 'index.php'; return false;" />
</form>
</body>
</html>
