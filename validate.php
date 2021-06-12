<?php
// checking for flash messages
function flashMessages() {
if(isset($_SESSION['error']))
{
    echo ('<p style = "color:red">'.htmlentities($_SESSION['error']).'</p>');
    unset ($_SESSION['error']);
}
if(isset($_SESSION['success']))
{
    echo ('<p style = "color:green">'.htmlentites($_SESSION['success']).'</p>');
    unset ($_SESSION['success']);
}
}

// validating profiles
function validateProfile () {
    if($_POST['fname'] == "" || $_POST['lname'] == "" || $_POST['email'] == "" || $_POST['headline'] == "" || $_POST['summary'] == "")
{
    return "All input fields must be filled";
}
elseif(strpos($_POST['email'], '@') == false)
{
    return "Email must contain @";
}
return true;
}
?>