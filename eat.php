<?php
require_once './api/connection.php';
if (!isset($_SESSION)) {
    session_start();
}
if (!$_SESSION['loggedin'] || $_SESSION['usertype'] != 's') {
    header("Location: login.php");
}
$Date = date("Y/m/d");
$Day = date("l");
$uid = $_SESSION['id'];
$eatingState = true;
$sql = "select * from adds where id = $uid and day = CURRENT_DATE();";
$res = $conn->query($sql);
if ($res && $row = $res->fetch_assoc()) {
if($row['stat'] == "F")
$eatingState = false;
} else { // insert
$sql = "insert into adds values ($uid, 'T', CURRENT_DATE());";
$res = $conn->query($sql);
$eatingState  = true;
}

if(isset($_POST['back'])) {
header("Location: s.php");
}

if(isset($_POST['yesE']))
{
    echo "fdd";
    $sql = "update adds set stat = 'T' where id = $uid and day = CURRENT_DATE();";
    $res = $conn->query($sql);
    $eatingState = true;
    header("Location: s.php");

}

if(isset($_POST['noE']))
{    
    $sql = "update adds set stat = 'F' where id = $uid and day = CURRENT_DATE();";
    $res = $conn->query($sql);
    $eatingState = false;
    header("Location: s.php");
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset=utf-8" />
    <title>NIT Andhra Pradesh MMS</title>
    <link rel="stylesheet" href="./css/s.css">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
</head>

<body style=" overflow:scroll;">
    <div class="alert alert-success" role="alert">
        Welcome to NIT AP MMS Dashboard <?php echo $_SESSION['name']; ?>
        <form action="s.php" method="post" style="float: right;">
            <input type="hidden" value="m" name="logout">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>




    <div id="content">
            <form action="eat.php" method="post" style="float: left;">
            <input type="hidden" value="m" name="back">
            <button type="submit" class="btn btn-primary">Back</button>
        </form>
        <div class="container" style="align-content: center;">
            <div class="card text-center">
                <div class="card-header">
                    Will you be eating in mess today
                </div>
            <div class="card-body">
                <?php if ($eatingState) : ?>
                    <h5 class="card-title">Present State: Yes</h5>
                <?php endif; ?>
                <?php if (!$eatingState) : ?>
                    <h5 class="card-title">Present State: No</h5>
                <?php endif; ?>
                <p class="card-text">Update your status and help us save food</p>
                <form method="POST" action="eat.php" style="float: right;">
                        <div class="form-group">
                            <input type="hidden" value="T" name="yesE" id ="yesE"> 
                        </div>
                        <button type="submit" class="btn btn-success">Yes</button>
                </form>

                <form method="POST" action="eat.php" style="float: left;">
                        <div class="form-group">
                            <input type="hidden" value="F" name="noE" id ="noE"> 
                        </div>
                        <button type="submit" class="btn btn-danger">No</button>
                </form>
                <br>
                <br>
                <br>

            </div>
            <div class="card-footer text-muted">
                 <?php echo $Date?> | <?php echo $Day?>
            </div>
            </div>



        </div>
    </div>

    <script src="./js/student/s.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>