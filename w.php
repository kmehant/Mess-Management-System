<?php
require_once './api/connection.php';
if (!isset($_SESSION)) {
    session_start();
}
if (!$_SESSION['loggedin'] || $_SESSION['usertype'] != 'w') {
    if ($_SESSION['usertype'] == 'cw'){}
    else {
    header("Location: login.php"); }
  
}

$usertyp = true;

if ($_SESSION['usertype'] == 'w')
$usertyp = false;


echo $usertype;
$Date = date("Y/m/d");
$Day = date("l");
$wid = $_SESSION['id'];

$sql = "SELECT * FROM users where id = $wid;";
$resff = $conn->query($sql);
$row23 = $resff->fetch_assoc();
$dp = $row23['path'];
if (isset($_POST['logout'])) {

    $_SESSION['loggedin'] = false;
    header("Location: login.php");
}
$stats = false;
$reset = false;
$sdetails = false;
$dpsection = false;
$payments = true;
$setMessFee = false;
$assignNew = false;
$mrd = false;
$paymentsData = "";
$sql = "SELECT * FROM payments";
$res = $conn->query($sql);
$paymentsData = $res;

if (isset($_POST['regnum'])) {
$regnum = $_POST['regnum'];
$rollnum = $_POST['rollnum'];
if ($regnum != "" && $rollnum != "")
{
        $sql = "SELECT * FROM payments where reg = $regnum and roll = $rollnum;";

}else
if ($regnum != ""){
    $sql = "SELECT * FROM payments where reg = $regnum;";
}
else if ($rollnum != "")
{
        $sql = "SELECT * FROM payments where roll = $rollnum;";
}else {
$sql = "SELECT * FROM payments"; }

$res = $conn->query($sql);
$paymentsData = $res;
    
}

if (isset($_POST['pay'])) {
    $payments = true;
    $dpsection = false;

    $assignNew = false;
    $reset = false;
$sdetails = false;
    $mrd = false;
    $setMessFee = false;
    $stats = false;
    $sql = "SELECT * FROM payments";
    $res = $conn->query($sql);
    $paymentsData = $res;
}
if (isset($_POST['mrd'])) {
    $mrd = true;
    $assignNew = false;
    $setMessFee = false;
$dpsection = false;

    $reset = false;
$sdetails = false;
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
$sdetails = false;
    $stats = false;
    $dpsection = false;

    $mrd = false;
    $setMessFee = false;


    $assignNew = false;

    $sql = "SELECT * FROM payments where status in ('M', 'F');";
    $res = $conn->query($sql);
    $paymentsData = $res;
}

if (isset($_POST['approved'])) {
    $payments = true;
    $mrd = false;
    $reset = false;
$sdetails = false;
$dpsection = false;

    $stats = false;
    $setMessFee = false;

    $assignNew = false;

    $sql = "SELECT * FROM payments where status = 'T';";
    $res = $conn->query($sql);
    $paymentsData = $res;
}




if (isset($_POST['approve'])) {
    $key = $_POST['approve'];

    $amma = $_POST['amma'];
    $sql = "update trans set status = 1 where id = $key;";
    $conn->query($sql);
        $ssreg = $_POST['sreg'];
    $sql = "select * from payments where reg = $ssreg";
    $resultr = $conn->query($sql);
    $row = $res->fetch_assoc();
    $rpaid = $row['paid'];
    $rpaid = $rpaid + $amma;
    $sql = "update payments set paid = $rpaid where reg = $ssreg;";
    $conn->query($sql);
    $sql = "select * from mess_amount order by id desc limit 1;";
    $resultr = $conn->query($sql);
    $row = $res->fetch_assoc();
    $tot = $row['amount'];
    if ($tot == $rpaid)
    {
    $sql = "update payments set status = 'T' where reg = $ssreg;";
    $conn->query($sql);
    }
    header("Location: w.php");
}


if (isset($_POST['reject'])) {
    $key = $_POST['reject'];
    $sql = "update trans set status = 2 where id = $key";
    $conn->query($sql);

    
    
    header("Location: w.php");
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
    $sdetails = false;
    $dpsection = false;

    $setMessFee = false;


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
    $setMessFee = false;

    $stats = true;
    $dpsection = false;

    $mrd = false;
    $sdetails = false;
    $assignNew = false;
    $reset = false;
}

if (isset($_POST['assignNew'])) {
    $payments  = false;
    $setMessFee = false;
$dpsection = false;

    $stats = false;
    $sdetails = false;
    $mrd = false;
    $assignNew = true;
    $reset = false;
}
if (isset($_POST['resetPass'])) {
    $newpass  = $_POST['resetPass'];
    $sql = "update users set password = '$newpass' where id = $wid;";
    $conn->query($sql);
    echo "<script type='text/javascript'>alert('Updated!');</script>";
    header("refresh:5;url=w.php");
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
        $setMessFee = false;
        $dpsection = false;


    $mrd = false;
    $sdetails = false;
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
        header("refresh:5;url=w.php");
    }
    $sql = "select * from users where email = '$email1' and password = '$pass';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $mid = $row['id'];
    $sql = "insert into mrdetails values ('$mid', '$name', '$email2', '$mn', 5);";
    $conn->query($sql);
    echo "<div class='alert alert-success' role='alert'>Assigned!</div>";

    $payments  = false;
    $sdetails = false;
    $stats = false;
        $setMessFee = false;
$dpsection = false;

    $reset = false;

    $mrd = false;
    $assignNew = true;
}
$detres = "";
$mainreg = "";
$totamount = "";
$pamount = "";
$pending = "";
if (isset($_POST['details'])) {
$ssid = $_POST['details'];
    $sql = "select * from payments where id = $ssid;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $regn = $row['reg'];
    if ($pending == 0 || $pending == "0")
    {
    $sql = "update payments set status = 'T' where reg = $regn;";
    $conn->query($sql); 
    }
    $sql = "select * from trans where reg = $regn;";
    
    $detres = $conn->query($sql);
    $row = $detres->fetch_assoc();
    $mainreg = $row['reg'];
    $detres = $conn->query($sql);
        $sql = "select * from mess_amount order by id desc limit 1;";
            $dres = $conn->query($sql);
        $rr = $dres->fetch_assoc();
        $totamount = $rr['amount'];
                $sql = "select * from payments where id = $ssid;";
            $dres = $conn->query($sql);
        $rr = $dres->fetch_assoc();
        $pamount = $rr['paid'];
        $pending = $totamount - $pamount;

        $payments  = false;
    $dpsection = false;

    $sdetails = true;
    $stats = false;
        $setMessFee = false;

    $reset = false;
    $mrd = false;
    $assignNew = false;
}
if (isset($_POST['backback'])) {

    header("Location: w.php");
}


if (isset($_POST['dp'])){
        $payments  = false;
    $setMessFee = false;
    $stats = false;
    $sdetails = false;
    $mrd = false;
    $dpsection = true;

    $assignNew = false;
    $reset = false;
    
    
}
if (isset($_POST['setMessFee'])){
        $payments  = false;
    $setMessFee = true;
    $stats = false;
    $sdetails = false;
    $mrd = false;
    $dpsection = false;

    $assignNew = false;
    $reset = false;
    
    
}


if (isset($_POST['messFee'])){
    $pysa = $_POST['messFee'];
    $sql = "insert into mess_amount (amount) values ($pysa);";
    $conn->query($sql);
    echo "<script type='text/javascript'>alert('Added!');</script>";
    header("refresh:5;url=w.php");
    
}



if (isset($_POST['dpupload'])) {
    $file  = $_FILES['dpupload'];

    $filename = $_FILES['dpupload']['name'];
    echo  $filename;
    $filetmpname = $_FILES['dpupload']['tmp_name'];
    $filesize = $_FILES['dpupload']['size'];
    $fileerror = $_FILES['dpupload']['error'];
    $filetype = $_FILES['dpupload']['type'];

    $fileext = explode('.', $filename);
    $fileaext = strtolower(end($fileext));

    $allowed = array('jpg', 'jpeg', 'png');

    if (in_array($fileaext, $allowed)) {
        if ($fileerror == 0) {
            if ($filesize < 5000000) {
                $filenewname  = uniqid('', true) . '.' . $fileaext;
                $filedestination = './uploads/profile/' . $filenewname;
                move_uploaded_file($filetmpname, $filedestination);
                $sql = "update users set path = '$filedestination' where id = $wid";
                $conn->query($sql);
                echo "<script type='text/javascript'>alert('Success!');</script>";
                header("refresh:5;url=w.php");
            } else {
                echo "<script type='text/javascript'>alert('Upload a file less than 5MB');</script>";
                header("refresh:5;url=w.php");
            }
        } else {
            echo "<script type='text/javascript'>alert('There is some problem');</script>";
            header("refresh:5;url=w.php");
        }
    } else {
        echo "<script type='text/javascript'>alert('You cannot upload this file.');</script>";
        header("refresh:5;url=w.php");
    }
}


$mrdimage = "";
    $sql = "SELECT * FROM mrdetails ORDER BY id DESC LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $n = $row['name'];
        $iden = $row['id'];
        $e = $row['email'];
        $p = $row['pno'];
        $r = $row['rating'];
        $sql = "SELECT * FROM users where id = $iden;";
    $resres = $conn->query($sql);
    $rowrow = $resres->fetch_assoc();
    $mrdimage = $rowrow['path'];
    } else {
        //error
        echo "<div class='alert alert-danger' role='alert'> No Mess Admin assigned yet</div>";
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
    <img src="<?php echo $dp; ?>" style = "height: 50px; width: 50px;"> &nbsp; &nbsp;
        Welcome to NIT AP MMS Dashboard <?php echo $_SESSION['name']; ?>
        <form action="w.php" method="post" style="float: right;">
            <input type="hidden" value="m" name="logout">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>


    <div id="slide-panel">
        <a href="#" class="btn btn-primary" id="opener">
            <i class="glyphicon glyphicon-align-justify"></i>
        </a>
        <br>

        <form action="w.php" method="post">
            <input type="hidden" value="m" name="pay">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Review Payment Details</button>
        </form>
        <br>

        <form action="w.php" method="post">
            <input type="hidden" value="m" name="stats">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Show Stats</button>
        </form>
        <br>

        <form action="w.php" method="post">
            <input type="hidden" value="m" name="mrd">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Mess Admin Details</button>
        </form>
        <br>
        <form action="w.php" method="post">
            <input type="hidden" value="m" name="assignNew">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Assign New Mess Admin</button>
        </form>
        <br>
           
                <form action="w.php" method="post">
            <input type="hidden" value="m" name="setMessFee">
                 <?php if($usertyp) : ?>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Set Mess Fee</button> <?php endif; ?>
        </form>
        <br>
        
        <form action="w.php" method="post">
            <input type="hidden" value="m" name="reset">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
        </form>
                       <br>
        <form action="w.php" method="post">
            <input type="hidden" value="m" name="dp">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Profile Photo</button>
        </form>

    </div>

    <div id="content">
    
                <div class="container">
            <?php if ($dpsection) : ?>
                <h2>Add or update your profile picture</h2>
                <img src="<?php echo $dp; ?>" class="rounded img-fluid" style="width: 100px; height: 100px;" alt="dp" />
                            <form method="POST" action="w.php" enctype="multipart/form-data">
                                    <input type="file" class="custom-file-input" id="dpupload" name="dpupload">
                                    <button type="submit" name="dpupload" class="btn btn-primary">Upload and Update</button>
                                </form>
                
            <?php endif; ?>
        </div>
       <div class="container text-align-center" style="text-align: center;" >
            <?php if ($setMessFee) : ?>
               <h3 style= "text-align: center;"> Set Mess Fee</h3>
                    <form action="w.php" method="post">
           
            <input style= "width: 200px" type="number" value="8000" name="messFee">
            <button type="submit" class="btn btn-primary" style="width: 80px;">Set</button>
        </form>


            <?php endif; ?>
        </div>
    
    
    
    
    <div class="container" style="max-height: 500px;  overflow:scroll;">
            <?php if ($sdetails) : ?>
               <form action="w.php" method="post" style ="float: left;">
                                <input type="hidden"" name="backback">
                                <button type="submit" class="btn btn-primary" style="width: 100%;">Back</button>
                            </form>
                <div class="container text-align-center" style="text-align:center;">
                <h3>Transaction details for Reg No: <?php echo $mainreg; ?></h3>
                
                <h3>Total Mess fee: <?php echo $totamount; ?></h3>
                                <h3>Amount Paid <?php echo $pamount; ?></h3>
                                                                <h3>Pending Amount <?php echo $pending; ?></h3>
                                                                



<table class="table">
  <thead>
    <tr>
      <th scope="Registration Number"> Registration Number</th>
      <th scope="col">Date</th>
      <th scope="col">Amount</th>
     <th scope="col">Status</th>
     <th scope="col">Proof</th>
          <th scope="col">Action</th>
          <th scope="col">Action</th>

    </tr>
  </thead>
  <tbody>
  <?php
                        while ($row = $detres->fetch_array()) :
                        ?>
                           <tr>
                           <td> <a href="#" class="list-group-item list-group-item-primary"><?php echo $row['reg']; ?></a></td>
                            <td><a href="#" class="list-group-item list-group-item-primary"> <?php echo $row['kab']; ?></a></td>
                                                        <td><a href="#" class="list-group-item list-group-item-primary"> <?php echo $row['amount']; ?></a></td>

                            <td><a href="#" class="list-group-item list-group-item-primary"> <?php if ($row['status'] == 0) {
                                                                                                        echo "Pending";
                                                                                                    }
                                                                                                    if ($row['status'] == 1) {
                                                                                                        echo "Approved";
                                                                                                    }
                                                                                                    if ($row['status'] == 2) {
                                                                                                        echo "Rejected";
                                                                                                    } ?></a></td>
                            
                          
                        <td><a target="_blank" rel="noopener noreferrer" href="<?php echo $row['path'];?>" class="list-group-item list-group-item-primary">Open Payment Proof</a></td>
                        <?php if ($row['status'] == 0) :?>
                          <td>
                           <form action="w.php" method="post">
                                <input type="hidden" value="<?php echo $row['id'];?>" name="reject">
                                <button type="submit" class="btn btn-danger" style="width: 100%;">Reject</button>
                            </form> </td>
                            <td><form action="w.php" method="post">
                                <input type="hidden" value="<?php echo $row['reg'];?>" name="sreg">
                                                                <input type="hidden" value="<?php echo $row['amount'];?>" name="amma">

                                <input type="hidden" value="<?php echo $row['id'];?>" name="approve">
                                <button type="submit" class="btn btn-success" style="width: 100%;">Approve</button>
                            </form>
                            </td>
                            <?php endif; ?>
                            
                                                    <?php if ($row['status'] == 1 || $row['status'] == 2 ) :?>
                          <td>
                          Action already performed
                            </td>
                            <?php endif; ?>
                            <br>
                            <br>

</tr>
                        <?php endwhile; ?>
                        
  </tbody>
</table>
                        
 
                </div>
            <?php endif; ?>

        </div> 
    
    
    
    
    
    
    
    
    
    
    
    
    
        <div class="container" style="max-height: 500px;  overflow:scroll;">
            <?php if ($payments) : ?>

                <h2 style=" text-align: center;">Approval Dashboard</h2>
                <h5 style=" text-align: center;">Please look through the requests to verify the mess payments</h5>
                <div class="container text-align-center" style="text-align:center;">

                    <form action="w.php" method="post" style="float:left;">
                        <input type="hidden" value="m" name="pending">
                        <button type="submit" class="btn btn-primary" style="width: 100%;"> Show Pending </button>
                    </form>

                    <form action="w.php" method="post" style="float:right;">
                        <input type="hidden" value="m" name="approved">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Show Approved</button>
                    </form>
                    
                    <br>
                    <br>
                <div class = "container text-align-center" style="text-align: center;" >
                <form action="w.php" method="post">
                    <div class="form-group">
                    <input placeholder="Registration Number" type="text" name="regnum">
                    <input placeholder="Roll Number" type="text" name="rollnum">
                     <button type="submit" class="btn btn-warning">Search</button>
                    </div>
                    </form>
                </div>
                    <br>
                    <br>
<table class="table">
  <thead>
    <tr>
      <th scope="Roll Number"> Roll Number</th>
      <th scope="col">Registration Number</th>
      <th scope="col">status</th>
     <th scope="col">Action</th>

    </tr>
  </thead>
  <tbody>
  <?php
                        while ($row = $paymentsData->fetch_array()) :
                        ?>
                           <tr>
                           <td> <a href="#" class="list-group-item list-group-item-primary"><?php echo $row['roll']; ?></a></td>
                            <td><a href="#" class="list-group-item list-group-item-primary"> <?php echo $row['reg']; ?></a></td>
                            <td><a href="#" class="list-group-item list-group-item-primary"> <?php if ($row['status'] == "F") {
                                                                                                        echo "Pending";
                                                                                                    }
                                                                                                    if ($row['status'] == "T") {
                                                                                                        echo "Completed";
                                                                                                    }
                                                                                                    if ($row['status'] == "M") {
                                                                                                        echo "To be reviewed";
                                                                                                    } ?></a></td>
                            
                            <td>
                           <form action="w.php" method="post">
                                <input type="hidden" value="<?php echo $row['id'];?>" name="details">
                                <button type="submit" class="btn btn-primary" style="width: 100%;">Details</button>
                            </form>
                            </td>
                            <br>
                            <br>

</tr>
                        <?php endwhile; ?>
                        
  </tbody>
</table>
                        
 
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

                    <form action="w.php" method="post">
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
                    <br>
                    <p style= "text-align: center;"> <img src="<?php echo $mrdimage; ?>" width=100px height=100px style="border-radius: 50%; "> </p>
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
                <form action="w.php" method="post">
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
            <?php if ($reset) : ?>
                <h3 style=" text-align:center;">Reset your password</h3>
                <p>Other details are handled by Admin, you can contact for any problems.</p>
                <form action="w.php" method="post">
                    <label for="exampleInputEmail1">Name: <?php echo $resetName; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Email: <?php echo $resetemail; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Mobile Number: <?php echo $resetpno; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Department: <?php echo $resetDepartment; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Hall: <?php echo $resetHall; ?></label>
                    <br>

                    <label for="exampleInputEmail1">Post: <?php echo $resetDesig; ?></label>
                    <br>
                    <div class="form-group">
                        <label for="exampleInputPassword1">New Password</label>
                        <input required type="password" class="form-control" name="resetPass" id="resetPass" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            <?php endif; ?>
        </div>


    </div>

    </div>




</body>

</html>