<?php
// loading some files
require_once ('server.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href="css/bootstrap.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>
<div class = "px-5 py-4">
<?php
// initializing some sessions
    if(!isset($_SESSION['name']))
    {
        if(!isset($_SESSION['ad']))
        {
            $_SESSION['once'] = 1;
        }
    ?>
        <h1>Nitin Sharma's Login Registry</h1>
        <?php
        // displaying errors
        if(isset($_SESSION['error']))
        {
            echo '<p style="color:red">';
            echo $_SESSION['error'];
            echo "</p>";
            unset($_SESSION['error']);
        }
        ?>
        <a href = "login.php">Kindly login</a>
    <?php
    // displaying data
    if(isset($_SESSION['largest-id']))
        {
            echo "<br>";
            echo "<br>";
            $q = 'SELECT * FROM profile WHERE profile_id > :id';
            $stm = $pdo->prepare($q);
            $stm -> execute(array(':id'=>$_SESSION['largest-id']));
            echo "<table border = 1>";
                echo "<tr><th>";
                echo "Name";
                echo "</th><th>";
                echo "Headline";
                echo "</th></tr>";
            while($row = $stm->fetch(PDO::FETCH_ASSOC))
            {
                echo "<tr><td>";
                echo '<a href="view.php?id='.$row['profile_id'].'">';
                echo $row['first_name'] . "    ";
                echo $row['last_name'];
                echo "</a>";
                echo "</td><td>";
                echo $row['headline'];
                echo "</td></tr>";
            }
                echo "</table>";
        }
    }
    else
    {
        ?>
        <h1>Nitin Sharma's Resume Registry</h1>
        <?php
        if(!isset($_SESSION['ad']))
        {
            $_SESSION['no-record'] = "No-record inserted";
        }

        // displaying error messages
        if(isset($_SESSION['error']))
        {
            echo '<p style = "color:red;">';
            echo $_SESSION['error'];
            echo "</p>";
            unset($_SESSION['error']);
        }

        // displaying success messages
        if(isset($_SESSION['success']))
        {
            echo '<p style = "color:green;">';
            echo $_SESSION['success'];
            echo "</p>";
            unset($_SESSION['success']);
        }
        ?>
        <a href = "logot.php">Logout</a>
       <br><br>
       <?php
       // displaying no record
       if(isset($_SESSION['no-record']))
       {
           echo $_SESSION['no-record'];
           echo "<br>";
           echo "<br>";
        }

        // displaying data in form of tables
        if(isset($_SESSION['largest-id']))
        {
            $q = 'SELECT * FROM profile WHERE profile_id > :id';
            $stm = $pdo->prepare($q);
            $stm -> execute(array(':id'=>$_SESSION['largest-id']));
            echo "<table border = 1>";
                echo "<tr><th>";
                echo "Name";
                echo "</th><th>";
                echo "Headline";
                echo "</th><th>";
                echo "Action";
                echo "</th></tr>";
            while($row = $stm->fetch(PDO::FETCH_ASSOC))
            {
                echo "<tr><td>";
                echo '<a href="view.php?id='.$row['profile_id'].'">';
                echo $row['first_name'] . "    ";
                echo $row['last_name'];
                echo "</a>";
                echo "</td><td>";
                echo $row['headline'];
                echo "</td><td>";
                foreach($row as $k => $v)
                {
                    if($_SESSION['user_id'] === $row['user_id'])
                    {
                        echo '<a href = "edit.php?id='.$row['profile_id'].'">Edit</a>'; echo "&nbsp;"; echo "&nbsp;"; echo "/"; echo "&nbsp;"; echo "&nbsp;";
                        echo '<a href = "delete.php?id='.$row['profile_id'].'">Delete</a>';
                        break;
                    }
                }
                $_SESSION['fname'] = $row['first_name'];
                $_SESSION['lname'] = $row['last_name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['headline'] = $row['headline'];
                $_SESSION['summary'] = $row['summary'];
            }
                echo "</td></tr>";
                echo "</table>";
        }
       ?>
        <br>
        <a href = "add.php">Add new entry</a> 
    <?php      
    }
    
    // fetching largest profile_id from profile table
    if(isset($_SESSION['once']))
    {
        $query = 'SELECT profile_id FROM profile ORDER BY profile_id desc limit 1';
        $stmt = $pdo->query($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['largest-id'] = $row['profile_id'];
        unset($_SESSION['once']);
    }
    ?>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    </div>

</body>
</html>