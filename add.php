<?php
// loading some files
require_once ('server.php');
session_start();
$fname = "";
$lname = "";
$email = "";
$headline = "";
$summary = "";

// checking user id
if(!isset($_SESSION['user_id']))
{
    die ("Kindly login");
}

// validating profile fields
if(isset($_POST['submit']))
{
    $fname = $_POST['fname'];
    $email = $_POST['email'];
    $headline = $_POST['headline'];
    $summary = $_POST['summary'];
    $lname = $_POST['lname'];
if($fname == "" || $lname == "" || $email == "" || $headline == "" || $summary == "" )
{
    $_SESSION['error'] = "All input fields must be filled";
    header('location:add.php');
    return;
}
elseif(strpos($email, '@') == false)
{
    $_SESSION['error'] = "Email must contain @";
    header('location:add.php');
    return;
}
}
    // adding position data
    if(isset($_POST['submit']) && isset($_POST['year']) && isset($_POST['area']))
    {
        $year = $_POST['year'];
        $description = $_POST['area'];

        // validating positions
        foreach ($description as $key => $val)
        {
            if($description[$key] == "" || $year[$key] == "")
        {
            $_SESSION['error'] = "Position fields are empty";
            header('location:add.php');
            return;
        }
        if(!is_numeric($year[$key]))
        {
            $_SESSION['error'] = "Year must be numeric";
            header('location:add.php');
            return;
        }
    }
}

// adding education values
if(isset($_POST['submit']) && isset($_POST['years']) && isset($_POST['school']))
{
    
    // defining some variables
    $years = $_POST['years'];
    $school = $_POST['school'];

    // validating position fields
    foreach ($school as $key => $val)
    {
        if($school[$key] == "" || $years[$key] == "")
    {
        $_SESSION['error'] = "Education fields are empty";
        header('location:add.php');
        return;
    }
    if(!is_numeric($years[$key]))
    {
        $_SESSION['error'] = "Year must be numeric";
        header('location:add.php');
        return;
    }
}
}

// inserting required data
if(isset($_POST['submit']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) )
{
    // adding profile data
    $query = 'INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:user_id, :fname, :lname, :email, :headline, :summary)';
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':user_id' => $_SESSION['user_id'], ':fname' => $fname, ':lname' => $lname, ':email' => $email, ':headline' => $headline, ':summary' => $summary));
    $profile_id = $pdo->lastInsertId();
    $_SESSION['profileId'] = $profile_id;
    unset ($_SESSION['no-record']);

if(isset($_POST['submit']) && isset($_POST['year']) && isset($_POST['area']))
{
    $rank = 1;
    // validating positions
    foreach ($description as $key => $val)
    {
        // inserting position values
            $q = 'INSERT INTO position (profile_id, year, description, rank) VALUES (:pid, :y, :d, :r)';
            $s = $pdo->prepare($q);
            $s->execute(array(':y' => $year[$key], ':d' => $description[$key], ':pid' => $profile_id, ':r' => $rank));
            $rank++;
    }
}

if(isset($_POST['submit']) && isset($_POST['years']) && isset($_POST['school']))
{
    $rank = 1;
    foreach ($school as $key => $val)
    {
        // loading institution_id
            $institution_id = false;
            $insti = 'SELECT institution_id FROM institution WHERE name = :name';
            $stmt = $pdo->prepare($insti);
            $stmt->execute(array(':name'=>$school[$key]));
            $myId = $stmt->fetch(PDO::FETCH_ASSOC);
            if($myId !== false)
            {
                $institution_id = $myId['institution_id'];
            }

            // adding institution to database if it does not exist
            if($myId['institution_id'] == "")
            {
                $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
                $stmt->execute(array(':name' => $school[$key]));
                $institution_id = $pdo->lastInsertId();
            }

            // inserting education entries
            $edu = 'INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:pid, :insti_id, :r, :y)';
            $stmt = $pdo->prepare($edu);
            $stmt->execute(array(':pid'=>$_SESSION['profileId'], ':insti_id' => $institution_id, ':r' => $rank, 'y'=> $years[$key]));
            $rank++;
    }
}

// displaying success message to user
$_SESSION['success'] = "Record Added";
$_SESSION['second'] = 2;
$_SESSION['ad'] = 1;       
header('location:index.php');
return;   
}
?>
<!-- html-->
<!DOCTYPE html>
<html lang="en">
<head>
<link rel = "stylesheet" href="css/bootstrap.min.css">
<link rel = "stylesheet" href="css/style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <script src = "jQuery.js"></script>
    <script src = "jquery-ui.js"></script>
</head>
<body>
<p style = "font-size:40px; margin-left:40px; margin-top:20px">
Adding profile for 
    <?php
    // greeting user
        echo $_SESSION['name'];
    ?>
</p>
<div class = "mx-2">
<?php
// displaying error
if(isset($_SESSION['error']))
{
    echo '<p style = "color:red;">';
    echo $_SESSION['error'];
    echo "</p>";
    unset($_SESSION['error']);
}
?>
<!--forms -->
<form method = "POST">
    <div class="form-group">
        <label for = "fname">First name</label>
        <input class = "form-control w-50" type = "text" id = "fname" name = "fname" value = "<?=htmlentities($fname)?>" autofocus/>
    </div>
    <div class = "form-group">
        <label for = "lname">Last Name</label>
        <input class = "form-control w-50" type = "text" id = "lname" name = "lname" value = "<?=htmlentities($lname)?>" />
    </div>
    <div class="form-group">
        <label for = "email">Email</label>
        <input class = "form-control w-50" type = "text" id = "email" name = "email" value = "<?=htmlentities($email)?>" />
    </div>
    <div class="form-group">
        <label for = "headline">Headline</label>
        <input class = "form-control w-50" type = "text" id = "headline" name = "headline" value = "<?=htmlentities($headline)?>" />
    </div>
    <div class="form-group">
        <label for = "summary">Summary</label>
        <textarea class = "form-control w-50" id = "summary" rows = "10"; cols = "60" name = "summary" value = "<?=htmlentities($summary)?>" ></textarea>
    </div>
    <br>
<div class="form-group">
    <label for = "plus">Position:</label> 
    <input type = "submit" name = "plus" value = "+" id = "plus">
</div>
<div class = "insert-here"></div>    
<div class="form-group">
        <label for = "plus">Education:</label>
        <input type = "submit" name = "pluss" value = "+" id = "pluss">
</div>
<div class = "insert-education"></div>
<input type = "submit" id = "submit" name = "submit" value = "submit" style="display:inline-block" class="btn btn-outline-success col-2"/>
<input type = "button" id = "cancel" name = "cancel" style="display:inline-block" class = "btn btn-outline-danger col-2" value = "Cancel" onclick = "location.href='index.php'; return false;" />
</form>

<!-- javascript using jquery-->
<script>
$(document).ready(function() {
var count = 0;
max_count = 9;
counter = 0;

// defining position block
pr = '<div id = "insert" >' + '<div class = "form-group">' + '<label for = "year"> Year: &nbsp; </label>' +
    '<input class = "w-50 form-ctr" type = "text" name = "year[]" id = "year" />' +   
    '<input type = "submit" name = "minus" id = "minus" value = "-" />' +
    '<p>' + 
    '<textarea class = "form-control w-50" name = "area[]" id = "area" rows = "10" cols = "50" placeholder = "Enter Summary" ></textarea>' + 
    '</p>' +
    '</div>' + 
    '</div>';
    
    // defining education block
edu = '<div id = "insertt" >' + '<div class = "form-group">' + '<label for = "years"> Year: &nbsp; &nbsp; &nbsp; </label>' + 
    '<input  class = "w-50 form-ctr" type = "text" name = "years[]" id = "years" />' + 
    '<input type = "submit" name = "minuss" id = "minuss" value = "-" />' + '<br>' +
    '<label for = "school"> School: &nbsp; </label>' +
    '<input class = "w-50 form-ctr" type = "text" name = "school[]" id = "school" class = "years"/>' +
    '</div>' + 
    '</div>';

    // creating positions dynamically
$("#plus").click(function(e) {
    e.preventDefault();
    if( count < max_count )
    {
        $(".insert-here").append(pr);
        $("input[id='year']").attr('id', 'year ' + count);
        $("textarea[id = 'area']").attr('id', 'area ' + count);
        count++;
    }
    else {
        alert ("max limit of input positions is 9 REACHED");
    }
});

// removining dynamically added positions
$('.insert-here').on('click', '#minus', function() {
$(this).closest('div').remove();
});

// adding education entries dynamically
$("#pluss").click(function(e) {
    e.preventDefault();
    if( counter < max_count )
    {
        $(".insert-education").append(edu);
        $("input[id='years']").attr('id', 'years ' + counter);
        $("input[id='school']").attr('id', 'school ' + counter);
        counter++;

        // auto completing school field
        $.getJSON('autocomplete.php', function(data, status){
        console.dir (data);
        $('.years').autocomplete({source : data}, {autofocus : true}); 
        });
    }
    else {
        alert ("max limit of input positions is 9 REACHED");
    }

});

// removing education entries
$('.insert-education').on('click', '#minuss', function() {
$(this).closest('div').remove();
});
});
</script>
<script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    </div>
</body>
</html>