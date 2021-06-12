<?php
//loading some files
require_once ('server.php');
session_start();

// checking for a valid id
if(!isset($_SESSION['user_id']))
{
    die ("Kindly login");
}
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

// checking if all input profile fields are filled
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
    header('location:edit.php?id='.$_GET['id']);
    return;
}
elseif(strpos($email, '@') == false)
{
    $_SESSION['error'] = "Email must contain @";
    header('location:edit.php?id='.$_GET['id']);
    return;
}
}
    // updating profile data
    if(isset($_POST['submit']) && isset($_POST['year']) && isset($_POST['area']))
    {

        // defining some variables
        $year = $_POST['year'];
        $description = $_POST['area'];
        foreach ($year as $key => $val)
        {

            // doing some checks
            if($description[$key] == "" || $year[$key] == "")
        {
            $_SESSION['error'] = "Position fields are empty";
            header('location:edit.php?id='.$_GET['id']);
            return;
        }
        if(!is_numeric($year[$key]))
        {
            $_SESSION['error'] = "Year must be numeric";
            header('location:edit.php?id='.$_GET['id']);
            return;
        }
    }
}

    // updating education entries
    if(isset($_POST['submit']) && isset($_POST['eduYear']) && isset($_POST['eduArea']))
    {
        // defining some variables
        $years = $_POST['eduYear'];
        $school = $_POST['eduArea'];

        foreach ($school as $key => $val)
        {
            // doing some checks
            if($school[$key] == "" || $years[$key] == "")
        {
            $_SESSION['error'] = "Education fields are empty";
            header('location:edit.php?id='.$_GET['id']);
            return;
        }
        if(!is_numeric($years[$key]))
        {
            $_SESSION['error'] = "Year must be numeric";
            header('location:edit.php?id='.$_GET['id']);
            return;
        }
    }
}

// updating data to be updated
if(isset($_POST['submit']) && isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
{

    // updating profile data
    $q = 'UPDATE profile SET first_name = :fname,
          last_name = :lname,
          email = :email,
          headline =  :headline,
          summary = :summary';
    $s = $pdo->prepare($q);
    $s->execute(array(':fname'=>$_POST['fname'], ':lname'=>$_POST['lname'], ':email'=>$_POST['email'], ':headline'=>$_POST['headline'], ':summary'=>$_POST['summary']));

if(isset($_POST['submit']) && isset($_POST['year']) && isset($_POST['area']))
    {
        $rank = 1;
        // deleting previous entries belonging to that profile id
        $query = 'DELETE FROM position WHERE profile_id = :pid';
        $statement = $pdo->prepare($query);
        $statement->execute(array(':pid'=> $_GET['id']));
        foreach ($year as $key => $val)
        {
        // inserting new position entries entries
            $q = 'INSERT INTO position (profile_id, year, description, rank) VALUES (:pid, :y, :d, :r)';
            $s = $pdo->prepare($q);
            $s->execute(array(':y' => $val, ':d' => $description[$key], ':pid' => $_GET['id'], ':r' => $rank));
            $rank++;
        }
    }

if(isset($_POST['submit']) && isset($_POST['eduYear']) && isset($_POST['eduArea']))
{
    $rank = 1;
    // deleting pre defined education entries belonging to that profile_id
    $query = 'DELETE FROM education WHERE profile_id = :pid ';
    $statement = $pdo->prepare($query);
    $statement->execute(array(':pid' => $_GET['id']));

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

            // Inserting institution name it to database if it does not exist
            if($myId['institution_id'] == "")
            {
                $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
                $stmt->execute(array(':name' => $school[$key]));
                $institution_id = $pdo->lastInsertId();
            }
            // inserting new education entries
            $edu = 'INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:pid, :insti_id, :r, :y)';
            $stmt = $pdo->prepare($edu);
            $stmt->execute(array(':pid'=>$_GET['id'], ':insti_id' => $institution_id, ':r' => $rank, 'y'=> $years[$key]));
            $rank++;
    }
}
// displaying success message to the user
            $_SESSION['success'] = "Record updated";
            header('location:index.php');
            return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel = "stylesheet" href = "css/bootstrap.css">
    <link rel = "stylesheet" href = "css/style.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src = "jQuery.js"></script>
    <script src = "jquery-ui.js"></script>
</head>
<body>
<p style = "font-size:40px; margin-left:40px; margin-top:20px;">
Editing profile for 
    <?php 
        echo $_SESSION['name'];
        if(isset($_SESSION['error']))
        {
            echo '<p style = "color:red" >';
            echo $_SESSION['error'];
            echo "</p>";
            unset ($_SESSION['error']);
        }
    ?>
</p>
<div class = "mx-3">
    <!--form-->
<form method = "POST">
    <div class="form-group">
        <label for = "fname">First name</label>
        <input type = "text" id = "fname" class = "form-control w-50" name = "fname" value = "<?=$_SESSION['fname']?>" autofocus />
    </div>
    <div class="form-group">
        <label for = "lname">Last Name</label>
        <input type = "text" id = "lname" class = "form-control w-50" name = "lname" value = "<?=$_SESSION['lname']?>" />
    </div>
    <div class="form-group">
        <label for = "email">Email</label>
        <input class = "form-control w-50" type = "text" id = "email" name = "email" value = "<?=$_SESSION['email']?>" />
    </div>
    <div class="form-group">
        <label for = "headline">Headline</label>
        <input class = "form-control w-50" type = "text" id = "headline" name = "headline" value = "<?=$_SESSION['headline']?>" />
    </div>
    <div class="form-group">
        <label for = "summary">Summary</label>
        <textarea id = "summary" class = "form-control w-50" rows = "10"; cols = "60" name = "summary" ><?=$_SESSION['summary']?></textarea>
    </div>
<?php
// loading position rank
    $a = 'SELECT rank FROM position WHERE profile_id = :jid ORDER BY rank desc limit 1';
    $b = $pdo->prepare($a);
    $b->execute(array(':jid'=>$_GET['id']));
    $c = $b->fetch(PDO::FETCH_ASSOC);
    $_SESSION['omid'] = $_GET['id'];

    // loading education rank
    $e = 'SELECT rank FROM education WHERE profile_id = :ppid ORDER BY rank desc limit 1';
    $edu = $pdo->prepare($e);
    $edu->execute(array(':ppid'=>$_GET['id']));
    $d = $edu->fetch(PDO::FETCH_ASSOC);
?>
<!-- defining position-->
<hr>
<div class="form-group">
    <label for = "plus">Position:</label> 
    <input type = "hidden" name = "hidden" class = "hidden" value = "<?= htmlentities($c['rank'])?>" />
    <input type = "submit" name = "plus" value = "+" id = "plus">
</div>
<div class = "insert-here"></div>
<!-- defining education-->
<hr>
<div class="form-group">
    <label for = "plus">Education:</label>
    <input type = "hidden" name = "eduHidden" class = "eduHidden" value = "<?= htmlentities($d['rank'])?>" />
    <input type = "submit" name = "addition" value = "+" id = "addition">
</div>
    <div class = "insert-Education"></div>
<input type = "submit" id = "submit" name = "submit" value = "submit" style="display:inline-block" class="btn btn-outline-success col-2" />
<input type = "button" id = "cancel" name = "cancel" value = "Cancel" style="display:inline-block" class = "btn btn-outline-danger col-2" onclick = "location.href='index.php'; return false;" />
</form>

<!-- jquery-->
<script>
$(document).ready(function() {
// defining a position block
er = '<div id = "insert" >' + '<div class = "form-group">' + '<label for = "year"> Year: &nbsp; </label>' +
    '<input class = "w-50 form-ctr" type = "text" name = "year[]" id = "year" />' + 
    '<input type = "submit" name = "minus" id = "minus" value = "-" />' +
    "<p>" +
    '<textarea class = "form-control w-50" name = "area[]" id = "area" rows = "10" cols = "50" placeholder = "Enter Summary" ></textarea>' + 
    "</p>" + 
    '</div>' + '</div>';

// defining an education block
dynamicEducation1 = '<div id = "insert" >' + '<div class = "form-group">' + '<label for = "eduYear"> Year: &nbsp; &nbsp; &nbsp; </label>' + 
    '<input class = "w-50 form-ctr" type = "text" name = "eduYear[]" id = "eduYear" value = "" />' + 
    '<input type = "submit" name = "eduMinus" id = "eduMinus" value = "-" />' + "<br>" +
    '<label for = "eduSchool"> School: &nbsp; </label>' +
    '<input class = "w-50 form-ctr" type = "text" name = "eduArea[]" id = "eduArea" class = "eduArea" value = "" />' + 
    '</div>' + '<div>';

// defining some variables
var count = $('.hidden').val(); 
var countEdu = $('.eduHidden').val();
max_count = 9;
var counter = 0;
var counter1 = 0;
var counterEdu = 0;
var counterEdu1 = 0;

// pre-loading position entries
    for (z = 0; z < count; z++)
    {
        pr = '<div id = "insert" >' + '<div class = "form-group">' + '<label for = "year"> Year: &nbsp; </label>' + 
        '<input class = "w-50 form-ctr" type = "text" name = "year[]" id = "year '+ z + '" value = "" />' + 
        '<input type = "submit" name = "minus" id = "minus" value = "-" />' +
        "<p>" +
        '<textarea class = "form-control w-50" name = "area[]" id = "area '+ z + '" rows = "10" cols = "50"></textarea>' + 
        "</p>" + 
        '</div>' + '</div>';
    
    $(".insert-here").append(pr); 

    // giving pre defined values to inserted position fields
    $.getJSON ('php.php', function(data, status) {
    $("input[id = 'year "+ counter +"']").attr('value', data[counter]["year"]);
        counter++;
        });

    // giving pre-defined values to inserted position fields
        $.getJSON ('php1.php', function(data1, status) {
    $("textarea[id = 'area "+ counter1 +"']").val(data1[counter1]);
        counter1++;
        });    
     }

     // loading pre-filed education fields
     for (i = 0; i < $('.eduHidden').val(); i++)
    {
        dynamicEducation = '<div id = "insert" >' + '<div class = "form-group">' + '<label for = "eduYear"> Year: &nbsp; &nbsp; &nbsp; </label>' +  
    '<input type = "text" class = "w-50 form-ctr" name = "eduYear[]" id = "eduYear '+ i +'" value = "" />' + 
    '<input type = "submit" name = "eduMinus" id = "eduMinus" value = "-" />' + "<br>" +
    '<label for = "eduSchool"> School: &nbsp; </label>' +
    '<input class = "w-50 form-ctr" type = "text" name = "eduArea[]" id = "eduArea '+ i +'" class = eduArea value = "" />' + 
    '</div>' + '</div>';
    $(".insert-Education").append(dynamicEducation);

// giving pre filled values to inserted education fields  
    $.getJSON ('php2.php', function(data, status) {
    $("input[id = 'eduYear "+ counterEdu +"']").attr('value', data[counterEdu]["year"]);
        counterEdu++;
        });

// giving pre defined values to inserted education fields
        $.getJSON ('php3.php', function(data, status) {
    $("input[id = 'eduArea "+ counterEdu1 +"']").attr('value', data[counterEdu1]);
        counterEdu1++;
        }); 
        
        // autocompleting school fields  
        $.getJSON('autocomplete.php', function(data, status){
        console.dir (data);
        $('.eduArea').autocomplete({source : data}, {autofocus : true}); 
        }); 
     }

// adding position fields dynamically
    $("#plus").click(function(e) {
    e.preventDefault();
    remaining = max_count - count;
    if(remaining > 0)
    {
        $(".insert-here").append(er);
        $("input[id='year']").attr('id', 'year ' + count);
        $("textarea[id = 'area']").attr('id', 'area ' + count);
        count++;
    }
    else {
        alert ("max limit of input positions is 9 REACHED");
    }
});

// removing dynamically added position fields
$('.insert-here').on('click', '#minus', function() {
    $(this).closest('div').remove();
});

// dynamically adding education fields 
$("#addition").click(function(e) {
    e.preventDefault();
    remainingEdu = max_count - countEdu;
    if(remainingEdu > 0)
    {
        $(".insert-Education").append(dynamicEducation1);
        $("input[id='eduYear']").attr('id', 'eduYear ' + countEdu);
        $("textarea[id = 'eduArea']").attr('id', 'eduArea ' + countEdu);
        countEdu++;

        // Auto completing for input school fields added dynamically 
        $.getJSON('autocomplete.php', function(data, status){
        console.dir (data);
        $('.eduArea').autocomplete({source : data}, {autofocus : true}); 
        }); 
    }
    else {
        alert ("max limit of input positions is 9 REACHED");
    }
});

// removing dynamically added education fields
    $('.insert-Education').on('click', '#eduMinus', function() {
        $(this).closest('div').remove();
    });
});
</script>
</div>
</body>
</html>