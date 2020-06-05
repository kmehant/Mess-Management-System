<?php
require_once './api/connection.php';
if (!isset($_SESSION)) {
    session_start();
}
if (!$_SESSION['loggedin'] || $_SESSION['usertype'] != 'a') {

    header("Location: login.php"); 
}
$Date = date("Y/m/d");
$Day = date("l");
$wid = $_SESSION['id'];
if (isset($_POST['logout'])) {

    $_SESSION['loggedin'] = false;
    header("Location: login.php");
}

$cwe = false;
$we = false;
$se  = false;
$stats = true;
$reset = false;
$payments = false;
$assignNew = false;
$mrd = false;
$paymentsData = "";
$sql = "SELECT * FROM payments";
$res = $conn->query($sql);
$paymentsData = $res;
if (isset($_POST['pay'])) {
    $payments = true;
    $assignNew = false;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = false;
    $mrd = false;
    $stats = false;
    $sql = "SELECT * FROM payments";
    $res = $conn->query($sql);
    $paymentsData = $res;
}
if (isset($_POST['mrd'])) {
    $mrd = true;
    $assignNew = false;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = false;
    $payments = false;
    $stats  = false;
    $n = "";
    $e = "";
    $p = 0;
    $r = 0;
    $sql = "SELECT * FROM mrdetails ORDER BY id DESC LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $n = $row['name'];
        $e = $row['email'];
        $p = $row['pno'];
        $r = $row['rating'];
    } else {
        //error
        echo "<div class='alert alert-danger' role='alert'> No Mess Admin assigned yet</div>";
    }
}

if (isset($_POST['pending'])) {
    $payments = true;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = false;
    $stats = false;
    $mrd = false;

    $assignNew = false;

    $sql = "SELECT * FROM payments where status in ('M', 'F');";
    $res = $conn->query($sql);
    $paymentsData = $res;
}

if (isset($_POST['approved'])) {
    $payments = true;
    $mrd = false;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = false;
    $stats = false;
    $assignNew = false;

    $sql = "SELECT * FROM payments where status = 'T';";
    $res = $conn->query($sql);
    $paymentsData = $res;
}

if (isset($_POST['approve'])) {
    $key = $_POST['approve'];
    $sql = "update payments set status = 'T' where id = $key";
    $conn->query($sql);
    header("Location: a.php");
}


if (isset($_POST['reject'])) {
    $key = $_POST['reject'];
    $sql = "update payments set status = 'F' where id = $key";
    $conn->query($sql);
    header("Location: a.php");
}

$sql = "select count(*) from payments;";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$totalNumberOfPayments = $row['count(*)'];
$sql = "select count(*) from payments where status = 'T';";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$NumberOfTPayments = $row['count(*)'];
$sql = "select count(*) from payments where status in ('F', 'M');";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$NumberOfFPayments = $row['count(*)'];
$totalNumberOfPayments = $row['count(*)'];
if (isset($_POST['stats'])) {
    $stats = true;
    $payments = false;
    $assignNew = false;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = false;
    $mrd = false;
    $sql = "select count(*) from payments;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $totalNumberOfPayments = $row['count(*)'];
    $sql = "select count(*) from payments where status = 'T';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $NumberOfTPayments = $row['count(*)'];
    $sql = "select count(*) from payments where status in ('F', 'M');";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $NumberOfFPayments = $row['count(*)'];
}
$b = 0;
$d = 0;
$l = 0;
$preferedDay  = "Choose a date below";
$s = 0;
if (isset($_POST['messFood'])) {
    $preferedDay = $_POST['messFood'];
    $sql = "select avg(breakfast) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $b = $row['avg(breakfast)'];
    $sql = "select avg(lunch) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $l = $row['avg(lunch)'];
    $sql = "select avg(dinner) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $d = $row['avg(dinner)'];
    $sql = "select avg(snacks) from mratings where day = '$preferedDay';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $s = $row['avg(snacks)'];
    $payments  = false;
    $stats = true;
    $mrd = false;
    $cwe = false;
$we = false;
$se  = false;
    $assignNew = false;
    $reset = false;
}

if (isset($_POST['assignNew'])) {
    $payments  = false;
    $stats = false;
    $mrd = false;
    $cwe = false;
$we = false;
$se  = false;
    $assignNew = true;
    $reset = false;
}
if (isset($_POST['resetPass'])) {
    $newpass  = $_POST['resetPass'];
    $sql = "update users set password = '$newpass' where id = $wid;";
    $conn->query($sql);
    echo "<script type='text/javascript'>alert('Updated!');</script>";
    header("refresh:5;url=a.php");
}
$resetDepartment = "";
$resetHall = "";
$resetName = "";
$resetpno = "";
$resetemail = "";
$resetDesig = "";
if (isset($_POST['reset'])) {

    $payments  = false;
    $stats = false;
    $cwe = false;
$we = false;
$se  = false;
    $mrd = false;
    $assignNew = false;
    $reset = true;
    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and users.id = $wid;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $resetDepartment = $row['department'];
    $resetHall = $row['hall'];
    $resetName = $row['name'];
    $resetpno = $row['pno'];
    $resetemail = $row['email'];
    if ($row['usertype'] == 'w')
        $resetDesig = "Warden";
    else {
        $resetDesig = "Chief Warden";
    }
}

if (isset($_POST['assignNewMR'])) {
    $email1 = $_POST['assignNewMR'];
    $email2 = $_POST['email2'];
    $mn = $_POST['mn'];
    $name = $_POST['name'];
    $pass = $_POST['pass'];
    $sql = "insert into users (email, password, usertype) values ('$email1', '$pass', 'ma');";
    $res = $conn->query($sql);
    if (!$res) {
        echo "<script type='text/javascript'>alert('Try unique values!');</script>";
        header("refresh:5;url=a.php");
    }
    $sql = "select * from users where email = '$email1' and password = '$pass';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $mid = $row['id'];
    $sql = "insert into mrdetails values ('$mid', '$name', '$email2', '$mn', 5);";
    $conn->query($sql);
    echo "<div class='alert alert-success' role='alert'>Assigned!</div>";

    $payments  = false;
    $stats = false;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = false;
    $mrd = false;
    $assignNew = true;
}
$cwdata = "";
if (isset($_POST['cwe']))
{
    $payments  = false;
    $stats = false;
    $reset = false;
    $cwe = true;
    $we = false;
    $se  = false;
    $mrd = false;
    $assignNew = false;
    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and usertype = 'cw';";
    $res = $conn->query($sql);
    $cwdata = $res;
}

$wdata = "";
if (isset($_POST['we']))
{
    $payments  = false;
    $stats = false;
    $reset = false;
    $cwe = false;
    $we = true;
    $se  = false;
    $mrd = false;
    $assignNew = false;
    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and usertype = 'w';";
    $res = $conn->query($sql);
    $wdata = $res;
}
$sdata = "";
if (isset($_POST['se']))
{
    $payments  = false;
    $stats = false;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = true;
    $mrd = false;
    $assignNew = false;
    $sql = "select * from users, payments where users.id = payments.id;";
    $res = $conn->query($sql);
    $sdata = $res;
}


if (isset($_POST['cwemailadd']))
{
$email = $_POST['cwemailadd'];
$name = $_POST['name'];
$department = $_POST['department'];
$hall = $_POST['hall'];
$pass = $_POST['pass'];
$mn = $_POST['mn'];
$sql = "insert into users (email, password, usertype) values ('$email', '$pass', 'cw');";
$conn->query($sql);
$sql = "select * from users where email = '$email' and password = '$pass' and usertype = 'cw';";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$iden = $row['id'];
$sql = "insert into cwwdetails values ($iden, '$name', $mn, '$department', '$hall');";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = true;
$we = false;
$se  = false;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Added!');</script>";
header("refresh:5;url=a.php");

}
if (isset($_POST['ident'])) // update a cw
{
$iden = $_POST['ident'];
$email = $_POST['email'];
$name = $_POST['name'];
$department = $_POST['department'];
$hall = $_POST['hall'];
$pass = $_POST['pass'];
$mn = $_POST['mn'];
$sql = "update users set email = '$email', password = '$pass' where id = $iden;";
$conn->query($sql);
$sql = "update cwwdetails set name = '$name', pno = '$mn', department = '$department', hall = '$hall' where id = $iden;";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = true;
$we = false;
$se  = false;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Updated!');</script>";
header("refresh:5;url=a.php");

}
if (isset($_POST['del'])) // delete a cw
{
$iden = $_POST['del'];
$sql = "delete from cwwdetails where id = $iden;";
$conn->query($sql);
$sql = "delete from users where id = $iden;";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = true;
$we = false;
$se  = false;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Deleted!');</script>";
header("refresh:5;url=a.php");
}




if (isset($_POST['wemailadd']))
{
$email = $_POST['wemailadd'];
$name = $_POST['name'];
$department = $_POST['department'];
$hall = $_POST['hall'];
$pass = $_POST['pass'];
$mn = $_POST['mn'];
$sql = "insert into users (email, password, usertype) values ('$email', '$pass', 'cw');";
$conn->query($sql);
$sql = "select * from users where email = '$email' and password = '$pass' and usertype = 'w';";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$iden = $row['id'];
$sql = "insert into cwwdetails values ($iden, '$name', $mn, '$department', '$hall');";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = false;
$we = true;
$se  = false;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Added!');</script>";
header("refresh:5;url=a.php");

}
if (isset($_POST['wident'])) // update a w
{
$iden = $_POST['wident'];
$email = $_POST['email'];
$name = $_POST['name'];
$department = $_POST['department'];
$hall = $_POST['hall'];
$pass = $_POST['pass'];
$mn = $_POST['mn'];
$sql = "update users set email = '$email', password = '$pass' where id = $iden;";
$conn->query($sql);
$sql = "update cwwdetails set name = '$name', pno = '$mn', department = '$department', hall = '$hall' where id = $iden;";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = false;
$we = true;
$se  = false;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Updated!');</script>";
header("refresh:5;url=a.php");

}
if (isset($_POST['wdel'])) // delete a w
{
$iden = $_POST['wdel'];
$sql = "delete from cwwdetails where id = $iden;";
$conn->query($sql);
$sql = "delete from users where id = $iden;";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = false;
$we = true;
$se  = false;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Deleted!');</script>";
header("refresh:5;url=a.php");
}




if (isset($_POST['semailadd']))
{
$email = $_POST['semailadd'];
$pass = $_POST['pass'];
$reg = $_POST['reg'];
$roll = $_POST['roll'];
$sql = "insert into users (email, password, usertype) values ('$email', '$pass', 's');";
$conn->query($sql);
$sql = "select * from users where email = '$email' and password = '$pass' and usertype = 's';";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$iden = $row['id'];
$sql = "insert into payments values ($iden, 'F', './', $roll, $reg, 0);";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = false;
$we = false;
$se  = true;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Added!');</script>";
header("refresh:5;url=a.php");

}
if (isset($_POST['sident'])) // update a s
{
$iden = $_POST['sident'];
$email = $_POST['email'];
$reg = $_POST['reg'];
$roll = $_POST['roll'];
$pass = $_POST['pass'];
$sql = "update users set email = '$email', password = '$pass' where id = $iden;";
$conn->query($sql);
$sql = "update payments set reg = $reg, roll = $roll, where id = $iden;";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = false;
$we = false;
$se  = true;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Updated!');</script>";
header("refresh:5;url=a.php");

}
if (isset($_POST['sdel'])) // delete a s
{
$iden = $_POST['sdel'];
$sql = "delete from payments where id = $iden;";
$conn->query($sql);
$sql = "delete from users where id = $iden;";
$conn->query($sql);
$payments  = false;
$stats = false;
$reset = false;
$cwe = false;
$we = false;
$se  = true;
$mrd = false;
$assignNew = false;
echo "<script type='text/javascript'>alert('Deleted!');</script>";
header("refresh:5;url=a.php");
}



if (isset($_POST['cwkeyemail']))
{
    $payments  = false;
    $stats = false;
    $reset = false;
    $cwe = true;
    $we = false;
    $se  = false;
    $mrd = false;
    $assignNew = false;
    $email = $_POST['cwkeyemail'];
    $name = $_POST['cwkeyname'];
    $dep = $_POST['cwkeydep'];
    $hall = $_POST['cwkeyhall'];
    $mn = $_POST['cwkeymn'];

    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and usertype = 'cw'";

    if($email){
        $sql = $sql." and email = '$email'";
    }
    if($name){
        $sql = $sql." and name = '$name'";
    }
    if($dep){
        $sql = $sql." and department = '$dep'";
    }
    if($hall){
        $sql = $sql." and hall = '$hall'";
    }
    if($mn){
        $sql = $sql." and pno = $mn";
    }

    $sql = $sql.";";
    $res = $conn->query($sql);
    $wdata = $res;
 
}


if (isset($_POST['wkeyemail']))
{
    $payments  = false;
    $stats = false;
    $reset = false;
    $cwe = false;
    $we = true;
    $se  = false;
    $mrd = false;
    $assignNew = false;
    $email = $_POST['wkeyemail'];
    $name = $_POST['wkeyname'];
    $dep = $_POST['wkeydep'];
    $hall = $_POST['wkeyhall'];
    $mn = $_POST['wkeymn'];

    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and usertype = 'w'";

    if($email){
        $sql = $sql." and email = '$email'";
    }
    if($name){
        $sql = $sql." and name = '$name'";
    }
    if($dep){
        $sql = $sql." and department = '$dep'";
    }
    if($hall){
        $sql = $sql." and hall = '$hall'";
    }
    if($mn){
        $sql = $sql." and pno = $mn";
    }

    $sql = $sql.";";
    $res = $conn->query($sql);
    $wdata = $res;
 
}
if (isset($_POST['skeyemail']))
{
    $payments  = false;
    $stats = false;
    $reset = false;
    $cwe = false;
    $we = false;
    $se  = true;
    $mrd = false;
    $assignNew = false;
    $email = $_POST['skeyemail'];
    $roll = $_POST['skeyroll'];
    $reg = $_POST['skeyreg'];
    $sql = "select * from users, payments where users.id = payments.id";

    if($reg){
        $sql = $sql." and reg = $reg";
    }
    if($roll){
        $sql = $sql." and roll = $roll";
    }

    if($email){
        $sql = $sql." and email = '$email'";
    }
    $sql = $sql.";";

    $res = $conn->query($sql);
    $sdata = $res;
 
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset=utf-8" />
    <title>NIT Andhra Pradesh MMS</title>
    <link rel="stylesheet" href="./css/s.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">


<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
    <script>  
 $(document).ready(function(){  
      $('#export').click(function(){  
          var excel_data = document.getElementById("provisions");
          $(".provForm").hide();

          print('#provisions');
          $(".provForm").show();
      });  
 });  
 </script>  

    <script src="./js/student/s.js"></script>
</head>

<body style=" overflow:scroll;">
    <div class="alert alert-success" role="alert">
        Welcome to NIT AP MMS Dashboard <?php echo $_SESSION['name']; ?>
        <form action="a.php" method="post" style="float: right;">
            <input type="hidden" value="m" name="logout">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>


    <div id="slide-panel">
        <a href="#" class="btn btn-primary" id="opener">
            <i class="glyphicon glyphicon-align-justify"></i>
        </a>
        <br>
        <?php if(false) :?>
        <form action="a.php" method="post">
            <input type="hidden" value="m" name="pay">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Review Payment Details</button>
        </form>
        <?php endif; ?>
        <br>

        <form action="a.php" method="post">
            <input type="hidden" value="m" name="stats">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Show Stats</button>
        </form>
        <br>

        <form action="a.php" method="post">
            <input type="hidden" value="m" name="mrd">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Mess Admin Details</button>
        </form>
        <br>
        <form action="a.php" method="post">
            <input type="hidden" value="m" name="assignNew">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Assign New Mess Admin</button>
        </form>
        <br>
        <form action="a.php" method="post">
            <input type="hidden" value="m" name="cwe">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Chief Warden</button>
        </form>
        <br>
        <form action="a.php" method="post">
            <input type="hidden" value="m" name="we">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Warden</button>
        </form>
        <br>
        <form action="a.php" method="post">
            <input type="hidden" value="m" name="se">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Students</button>
        </form>
    </div>

    <div id="content">
        <div class="container" style="max-height: 500px;  overflow:scroll;">
            <?php if (false) : ?>

                <h2 style=" text-align: center;">Approval Dashboard</h2>
                <h5 style=" text-align: center;">Please look through the requests to verify the mess payments</h5>
                <div class="container text-align-center" style="text-align:center;">

                    <form action="a.php" method="post" style="float:left;">
                        <input type="hidden" value="m" name="pending">
                        <button type="submit" class="btn btn-primary" style="width: 100%;"> Show Pending </button>
                    </form>

                    <form action="a.php" method="post" style="float:right;">
                        <input type="hidden" value="m" name="approved">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Show Approved</button>
                    </form>
                    <br>
                    <br>
                    <div class="list-group w-100 align-items-center" style="max-width: 300px; margin: 0 auto;">

                        <?php
                        while ($row = $paymentsData->fetch_array()) :
                        ?>

                            <br>

                            <a href="#" class="list-group-item list-group-item-primary"> Roll Number: <?php echo $row['roll']; ?></a>
                            <a href="#" class="list-group-item list-group-item-primary"> Registration Number: <?php echo $row['reg']; ?></a>
                            <a href="#" class="list-group-item list-group-item-primary"> status: <?php if ($row['status'] == "F") {
                                                                                                        echo "Pending";
                                                                                                    }
                                                                                                    if ($row['status'] == "T") {
                                                                                                        echo "Completed";
                                                                                                    }
                                                                                                    if ($row['status'] == "M") {
                                                                                                        echo "To be reviewed";
                                                                                                    } ?></a>
                            <a href="<?php echo $row['path']; ?>" target="_blank" class="list-group-item list-group-item-info"> Open Payment Proof</a>
                            <br>
                            <form action="a.php" method="post">
                                <input type="hidden" value="<?php echo $row['id']; ?>" name="reject">
                                <button type="submit" class="btn btn-danger" style="width: 100%;">Reject</button>
                            </form>
                            <form action="a.php" method="post">
                                <input type="hidden" value="<?php echo $row['id']; ?>" name="approve">
                                <button type="submit" class="btn btn-success" style="width: 100%;">Approve</button>
                            </form>

                            <br>
                            <br>


                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="container">
            <?php if ($stats) : ?>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Student Mess Fee Payment Stats
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">Total: <?php echo $totalNumberOfPayments; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Pending: <?php echo $NumberOfFPayments; ?></a>
                    <a href="#" class="list-group-item list-group-item-action">Approved: <?php echo $NumberOfTPayments; ?></a>
                </div>

                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Mess Food Ratings on <?php echo $preferedDay; ?>
                    </a>

                    <form action="a.php" method="post">
                        <input type="date" value="m" name="messFood" required>
                        <button type="submit" class="btn btn-warning">Plot Ratings</button>
                    </form>

                    <canvas id="myChart" width="200" height="200"></canvas>
                    <script>
                        var ctx = document.getElementById('myChart').getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Breakfast', 'Lunch', 'Dinner', 'Snacks', 'reference'],
                                datasets: [{
                                    label: 'Rating',
                                    data: <?php echo "[" . $b . ", " . $l . ", " . $d . ", " . $s . ", 5" . "]" ?>,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)',
                                        'rgba(54, 162, 235, 0.2)',
                                        'rgba(255, 206, 86, 0.2)',
                                        'rgba(75, 192, 192, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    </script>
                </div>



            <?php endif; ?>
        </div>

        <div class="container">
            <?php if ($mrd) : ?>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Mess Admin Details
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">Name: <?php echo $n; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Email: <?php echo $e; ?></a>
                    <a href="#" class="list-group-item list-group-item-action">Phone Number: <?php echo $p; ?></a>
                    <a href="#" class="list-group-item list-group-item-action"> Average Rating: <?php echo $r; ?></a>
                </div>
            <?php endif; ?>
        </div>
        <div class="container">
            <?php if ($assignNew) : ?>
                <h3 style=" text-align:center;">Assign a new Mess Admin</h3>
                <p> As you assign a new mess admin you automatically remove the present one from the assignment</p>
                <form action="a.php" method="post">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="email" class="form-control" name="assignNewMR" id="assignNewMR" aria-describedby="emailHelp" placeholder="Enter Login Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address 2</label>
                        <input required type="email" class="form-control" name="email2" id="email2" aria-describedby="emailHelp" placeholder="Enter Contact Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input required type="text" class="form-control" name="name" id="name" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" class="form-control" name="pass" id="pass" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile Number</label>
                        <input required type="tel" class="form-control" name="mn" id="mn" aria-describedby="emailHelp" placeholder="Enter Mobile Number">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="container">
            <?php if ($cwe) : ?>
                <h3 style=" text-align:center;">Chief Warden Details</h3>
                <form action="a.php" method="post">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="email" class="form-control" name="cwemailadd" id="cwemailadd" aria-describedby="emailHelp" placeholder="Enter Login Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input required type="text" class="form-control" name="name" id="name" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Department</label>
                        <input required type="text" class="form-control" name="department" id="department" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Hall</label>
                        <input required type="text" class="form-control" name="hall" id="hall" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" class="form-control" name="pass" id="pass" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile Number</label>
                        <input required type="tel" class="form-control" name="mn" id="mn" aria-describedby="emailHelp" placeholder="Enter Mobile Number">
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
                <br>
                <br>
                <div class = "container text-align-center" style="text-align: center;" >
                <form action="a.php" method="post">
                    <div class="form-group">
                    <input placeholder="Email" type="email" name="cwkeyemail">
                    <input placeholder="Name" type="text" name="cwkeyname">
                    <input placeholder="Department" type="text" name="cwkeydep">
                    <input placeholder="Hall" type="text" name="cwkeyhall">
                    <input placeholder="Mobile Number" type="tel" name="cwkeymn">
                     <button type="submit" class="btn btn-warning">Search</button>
                    </div>
                    </form>
                </div>
                <div class="container" style="max-height: 500px;  overflow:scroll;">
                <?php
                    while ($row = $cwdata->fetch_array()) :
                    ?>
                    <br>
                                  <form action="a.php" method="post" >
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="hidden" placeholder="<?php echo $row['id'];?>" class="form-control" name="ident" id="ident" aria-describedby="emailHelp" >
                        <input required type="email" value="<?php echo $row['email'];?>" placeholder="<?php echo $row['email'];?>" class="form-control" name="email" id="email" aria-describedby="emailHelp">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input required type="text" value="<?php echo $row['name'];?>" placeholder="<?php echo $row['name'];?>" class="form-control" name="name" id="name" aria-describedby="emailHelp" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Department</label>
                        <input required type="text" value="<?php echo $row['department'];?>" placeholder="<?php echo $row['department'];?>" class="form-control" name="department" id="department" aria-describedby="emailHelp" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Hall</label>
                        <input required type="text" value="<?php echo $row['hall'];?>" placeholder="<?php echo $row['hall'];?>" class="form-control" name="hall" id="hall" aria-describedby="emailHelp" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" value="<?php echo $row['password'];?>" placeholder="<?php echo $row['password'];?>" class="form-control" name="pass" id="pass" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile Number</label>
                        <input required type="tel" value="<?php echo $row['pno'];?>" placeholder="<?php echo $row['pno'];?>" class="form-control" name="mn" id="mn" aria-describedby="emailHelp" >
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    </form>
                    <form action="a.php" method="post">
                    <div class="form-group">
                        <input  type="hidden" value="<?php echo $row['id']; ?>" class="form-control" name="del" id="del" aria-describedby="emailHelp" >
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                    </form>

 
                <br>


                    <?php endwhile; ?>
                    </div>
            <?php endif; ?>
             
        </div>


        <div class="container">
            <?php if ($we) : ?>
                <h3 style=" text-align:center;">Warden Details</h3>
                <form action="a.php" method="post">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="email" class="form-control" name="wemailadd" id="wemailadd" aria-describedby="emailHelp" placeholder="Enter Login Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input required type="text" class="form-control" name="name" id="name" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Department</label>
                        <input required type="text" class="form-control" name="department" id="department" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Hall</label>
                        <input required type="text" class="form-control" name="hall" id="hall" aria-describedby="emailHelp" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" class="form-control" name="pass" id="pass" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile Number</label>
                        <input required type="tel" class="form-control" name="mn" id="mn" aria-describedby="emailHelp" placeholder="Enter Mobile Number">
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
                <br>
                <br>
                <div class = "container text-align-center" style="text-align: center;" >
                <form action="a.php" method="post">
                    <div class="form-group">
                    <input placeholder="Email" type="email" name="wkeyemail">
                    <input placeholder="Name" type="text" name="wkeyname">
                    <input placeholder="Department" type="text" name="wkeydep">
                    <input placeholder="Hall" type="text" name="wkeyhall">
                    <input placeholder="Mobile Number" type="tel" name="wkeymn">
                     <button type="submit" class="btn btn-warning">Search</button>
                    </div>
                    </form>
                </div>
                <div class="container" style="max-height: 500px;  overflow:scroll;">
                <?php
                    while ($row = $wdata->fetch_array()) :
                    ?>
                    <br>
                                  <form action="a.php" method="post" >
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="hidden" placeholder="<?php echo $row['id'];?>" class="form-control" name="wident" id="wident" aria-describedby="emailHelp" >
                        <input required type="email" value="<?php echo $row['email'];?>" placeholder="<?php echo $row['email'];?>" class="form-control" name="email" id="email" aria-describedby="emailHelp">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input required type="text" value="<?php echo $row['name'];?>" placeholder="<?php echo $row['name'];?>" class="form-control" name="name" id="name" aria-describedby="emailHelp" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Department</label>
                        <input required type="text" value="<?php echo $row['department'];?>" placeholder="<?php echo $row['department'];?>" class="form-control" name="department" id="department" aria-describedby="emailHelp" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Hall</label>
                        <input required type="text" value="<?php echo $row['hall'];?>" placeholder="<?php echo $row['hall'];?>" class="form-control" name="hall" id="hall" aria-describedby="emailHelp" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" value="<?php echo $row['password'];?>" placeholder="<?php echo $row['password'];?>" class="form-control" name="pass" id="pass" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile Number</label>
                        <input required type="tel" value="<?php echo $row['pno'];?>" placeholder="<?php echo $row['pno'];?>" class="form-control" name="mn" id="mn" aria-describedby="emailHelp" >
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    </form>
                    <form action="a.php" method="post">
                    <div class="form-group">
                        <input  type="hidden" value="<?php echo $row['id']; ?>" class="form-control" name="wdel" id="wdel" aria-describedby="emailHelp" >
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                    </form>

 
                <br>


                    <?php endwhile; ?>
                    </div>
            <?php endif; ?>
             
        </div>

        <div class="container">
            <?php if ($se) : ?>
                <h3 style=" text-align:center;">Add Student Details</h3>
                <form action="a.php" method="post">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="email" class="form-control" name="semailadd" id="semailadd" aria-describedby="emailHelp" placeholder="Enter Login Email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" class="form-control" name="pass" id="pass" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Registration Number</label>
                        <input required type="number" class="form-control" name="reg" id="reg" placeholder="Registration Number">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Roll Number</label>
                        <input required type="number" class="form-control" name="roll" id="roll" placeholder="Roll Number">
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
                <br>
                <br>
                <h3 style=" text-align:center;">Edit Existing Student Details</h3>

                <div class = "container text-align-center" style="text-align: center;" >
                <form action="a.php" method="post">
                    <div class="form-group">
                    <input placeholder="email" type="email" name="skeyemail">
                    <input placeholder="Roll Number" type="number" name="skeyroll">
                    <input placeholder="Registration Number" type="number" name="skeyreg">
                     <button type="submit" class="btn btn-warning">Search</button>
                    </div>
                    </form>
                </div>
                <div class="container" style="max-height: 500px;  overflow:scroll;">

                <?php
                    while ($row = $sdata->fetch_array()) :
                    ?>
                    <br>
                                  <form action="a.php" method="post" >
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input required type="hidden" placeholder="<?php echo $row['id'];?>" class="form-control" name="sident" id="sident" aria-describedby="emailHelp" >
                        <input required type="email" value="<?php echo $row['email'];?>" placeholder="<?php echo $row['email'];?>" class="form-control" name="email" id="email" aria-describedby="emailHelp">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Give Password</label>
                        <input required type="password" value="<?php echo $row['password'];?>" placeholder="<?php echo $row['password'];?>" class="form-control" name="pass" id="pass" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Roll Number:</label>
                        <input required type="number" value="<?php echo $row['roll'];?>" placeholder="<?php echo $row['roll'];?>" class="form-control" name="roll" id="roll" >
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Registration Number:</label>
                        <input required type="number" value="<?php echo $row['reg'];?>" placeholder="<?php echo $row['reg'];?>" class="form-control" name="reg" id="reg" >
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    </form>
                    <br>
                    <form action="a.php" method="post">
                    <div class="form-group">
                        <input  type="hidden" value="<?php echo $row['id']; ?>" class="form-control" name="sdel" id="sdel" aria-describedby="emailHelp" >
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                    </form>

 
                <br>


                    <?php endwhile; ?>
                    </div>
            <?php endif; ?>
             
        </div>
    </div>

    </div>




</body>

</html>