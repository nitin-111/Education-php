<?php
// loading files
require_once ('server.php');
session_start();

// defining salt
$salt = "XyZzy12*_";

// validating user
if(isset($_POST['submit']) && isset($_POST['email']) && isset($_POST['password']))
{
    $hashed = hash('md5', $salt.$_POST['password']);
    $query = 'SELECT * FROM users WHERE email = :em AND password = :pass';
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':em' => $_POST['email'], ':pass' => $hashed));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row)
    {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['success'] = "Login Successful";
        header('location:index.php');
        return;
    }    
    else
    {
        $_SESSION['error'] = "Incorrect password";
        header('location:login.php');
        return;
    }
}
?>
<h1>Please login </h1>
<?php

// displaying error
if(isset($_SESSION['error']))
{
    echo '<p style = "color:red">';
    echo $_SESSION['error'];
    echo "</p>";
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel = "stylesheet" href="css/bootstrap.min.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src = "jQuery.js"></script>
    <title>Login page</title>
</head>
<body>

<form method = "POST">
    <div class="form-group mx-2">
    <label for = "email">Email:</label>
    <input type = "text" class = "form-control" name = "email" id = "email"/>
    </div>
    <div class="form-group mx-2">
    <label for = "password">Password:</label>
    <input class = "form-control" type = "password" name = "password" id = "password"/>
    </div>
<input type = "submit" name = "submit" id = "submit" value = "submit" style="display:inline-block" class="btn btn-outline-success col-2"/>
<input type = "button" name="cancel" value = "Cancel" onclick = "location.href='index.php';return false" style="display:inline-block" class="btn btn-outline-danger col-2"/>
</form>
<!--email = umsi@umich.edu 
password = php123-->
<p>For a password hint, <b>view page source</b> and find an account and password hint in the HTML comments.</p>


<script>
// doing some checks with jquery
    $("#submit").click(function(event) {
        if( $.trim($("#email").val()) == '' || $.trim($("#password").val()) == '')
        {
            alert('Email and password should be filled');
            return false;
        }
        if( $("#email").val().indexOf('@') == -1 )
        {
            alert('@ should be present in email');
            return false;
        }
    });
</script>
<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
</body>
</html>