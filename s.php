<?php
require_once './api/connection.php';
if (!isset($_SESSION)) {
    session_start();
}

if (!$_SESSION['loggedin'] || $_SESSION['usertype'] != 's') {
    header("Location: login.php");
}
$foodSection = false;
$invent = false;
$sql = "SELECT * FROM mess_amount";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$total_amount = $row['amount'];

$sql = "SELECT * FROM timetable";
$menudata = $conn->query($sql);

$uid = $_SESSION['id'];
$rm = false;
$dpsection = false;
$menu = true;
$mrd = false;
$reset = false;
$cww = false;
$foodSection = false;
$invent = false;
$pay = false;
$c  = false;
$Date = date("Y-m-d");
$Day = date("l");
$sql = "SELECT * FROM payments where id = $uid;";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$regmain = $row['reg'];
$rollmain = $row['roll'];
$sql = "SELECT * FROM trans where reg = $regmain;";
$transactions = $conn->query($sql);

$messrepo  = false;
echo $messrepo;

$sql = "SELECT * FROM messrep;";
$reprep = $conn->query($sql);
    while ($reprep && $rrrow = $reprep->fetch_assoc()) {

    if ($rrrow['reg'] == $regmain)
    {
        $messrepo = true;
    }
    }
    

    
$sql = "SELECT * FROM users where id = $uid;";
$resff = $conn->query($sql);
$row23 = $resff->fetch_assoc();
$dp = $row23['path'];

if (isset($_POST['rm'])) {
    $rm = true;
    $dpsection = false;

    $reset = false;
    $foodSection = false;
$invent = false;
    $menu = false;
    $mrd = false;
    $cww = false;

    $pay = false;
    $c = false;

    $Date = date("Y/m/d");
    $Day = date("l");
}

if (isset($_POST['sr'])) {
    $sql = "SELECT * FROM mrdetails ORDER BY id DESC LIMIT 1;";
    $res = $conn->query($sql);
    $sr = $_POST['sr'];
    $br = $_POST['br'];
    $lr = $_POST['lr'];
    $dr = $_POST['dr'];


    if ($res && $row = $res->fetch_assoc()) {
        $ID = $row['id'];
        $sql = "SELECT * FROM mratings where day = CURRENT_DATE() and id = '$uid'";
        $res1 = $conn->query($sql);
        if ($res1 && $row2 = $res1->fetch_assoc()) { // update
            echo "<script type='text/javascript'>alert('Already Given, updated your response');</script>";
            $sql = "Update mratings set breakfast = $br, lunch = $lr, dinner = $dr, snacks = $sr where id = $uid and day = CURRENT_DATE() and mid = $ID;";
            $conn->query($sql);
        } else { // add
            $sql = "INSERT INTO mratings VALUES ($uid,$ID,$br,$lr,$sr,$dr, CURRENT_DATE());";
            $conn->query($sql);
            echo "<script type='text/javascript'>alert('Rating Given');</script>";
        }
    } else {
        //error
        echo "<div class='alert alert-danger' role='alert'> No mess admin assigned, so rating is unsuccessful </div>";
    }
}

if (isset($_POST['logout'])) {

    $_SESSION['loggedin'] = false;
    header("Location: login.php");
}
$mrdimage = "";
if (isset($_POST['mrd'])) {
    $rm = false;
    $mrd = true;
    $dpsection = false;
    $foodSection = false;
$invent = false;

    $menu = false;
    $pay = false;
    $c = false;
    $reset = false;

    $cww = false;

    $n = "";
    $e = "";
    $p = 0;
    $r = 0;
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
}

$cwwdata = "";
if (isset($_POST['cww'])) {
    $rm = false;
    $foodSection = false;
$invent = false;
    $mrd = false;
    $menu = false;
    $cww = true;
    $pay = false;
    $dpsection = false;

    $c = false;
    $reset = false;


    $sql = "select * from users, cwwdetails where users.id = cwwdetails.id and users.usertype in ('w','cw')";
    $res = $conn->query($sql);
    $cwwdata = $res;
}
$status = "F";

$paid_amount = 0;
$rem_amount = $total_amount - $paid_amount;
$sql = "SELECT * FROM payments where id = $uid;";
$res = $conn->query($sql);
$row = $res->fetch_assoc();
$paid_amount = $row['paid'];
$rem_amount = $total_amount - $paid_amount;

if (isset($_POST['p'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $reset = false;
    $dpsection = false;
$foodSection = false;
$invent = false;
    $cww = false;
    $pay = true;

    $c = false;
    $sql = "SELECT * FROM payments where id = $uid";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $status = $row['status'];
        $paid_amout = $row['paid'];
        
    } else {
        $sql = "SELECT * FROM users where id = $uid";
        $sql = "insert into payments values ($uid, 'F', './', )";
        $res = $conn->query($sql);
        echo "<script type='text/javascript'>alert('Setting things!');</script>";
        header("refresh:5;url=s.php");
    }
}



if (isset($_POST['rating'])) {
    $rating = ($_POST['rating']);
    $id = $_SESSION['id'];
    $sql = "SELECT * FROM ratings where id = '$id'";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) { // update
        $sql = "update ratings set rate = '$rating' where id = '$id'";
        $res2 = $conn->query($sql);
    } else {
        $sql = "insert into ratings values ('$id','$rating')";
        $res2 = $conn->query($sql);
    }
    echo "<script type='text/javascript'>alert('Rating Given');</script>";
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
                $sql = "update users set path = '$filedestination' where id = $uid";
                $conn->query($sql);
                echo "<script type='text/javascript'>alert('Success!');</script>";
                header("refresh:5;url=s.php");
            } else {
                echo "<script type='text/javascript'>alert('Upload a file less than 5MB');</script>";
                header("refresh:5;url=s.php");
            }
        } else {
            echo "<script type='text/javascript'>alert('There is some problem');</script>";
            header("refresh:5;url=s.php");
        }
    } else {
        echo "<script type='text/javascript'>alert('You cannot upload this file.');</script>";
        header("refresh:5;url=s.php");
    }
}
if (isset($_POST['fileupload'])) {
    $am = $_POST['paya'];
    $file  = $_FILES['fileupload'];

    $filename = $_FILES['fileupload']['name'];
    echo  $filename;
    $filetmpname = $_FILES['fileupload']['tmp_name'];
    $filesize = $_FILES['fileupload']['size'];
    $fileerror = $_FILES['fileupload']['error'];
    $filetype = $_FILES['fileupload']['type'];

    $fileext = explode('.', $filename);
    $fileaext = strtolower(end($fileext));

    $allowed = array('jpg', 'jpeg', 'png');

    if (in_array($fileaext, $allowed)) {
        if ($fileerror == 0) {
            if ($filesize < 5000000) {
                $filenewname  = uniqid('', true) . '.' . $fileaext;
                $filedestination = './uploads/payments/' . $filenewname;
                move_uploaded_file($filetmpname, $filedestination);
                $sql = "update payments set path = '$filedestination', status = 'M' where id = $uid";
                $conn->query($sql);
                $sql = "insert into trans (reg, path, kab, amount) values ($regmain, '$filedestination', CURRENT_DATE(), $am);";
                
                $conn->query($sql);
                echo "<script type='text/javascript'>alert('Success!');</script>";
                header("refresh:5;url=s.php");
            } else {
                echo "<script type='text/javascript'>alert('Upload a file less than 5MB');</script>";
                header("refresh:5;url=s.php");
            }
        } else {
            echo "<script type='text/javascript'>alert('There is some problem');</script>";
            header("refresh:5;url=s.php");
        }
    } else {
        echo "<script type='text/javascript'>alert('You cannot upload this file.');</script>";
        header("refresh:5;url=s.php");
    }
}



if (isset($_POST['comp'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = false;
    $foodSection = false;
$invent = false;
    $pay = false;
    $c = true;
    $reset = false;
    $dpsection = false;

}

if (isset($_POST['dp'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = false;
    $foodSection = false;
$invent = false;
    $pay = false;
    $c = false;
    $reset = false;
    $dpsection = true;

}


if (isset($_POST['foodSection'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = false;
    $foodSection = true;
$invent = false;
    $pay = false;
    $c = false;
    $reset = false;
    $dpsection = false;

}

if (isset($_POST['inventory'])) {
    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = false;
    $foodSection = false;
$invent = true;
    $pay = false;
    $c = false;
    $reset = false;
    $dpsection = false;

}

if (isset($_POST['addeating'])) {
    header("Location: eat.php");
}

if (isset($_POST['resetPass'])) {
    $newpass  = $_POST['resetPass'];
    $sql = "update users set password = '$newpass' where id = $uid;";
    $conn->query($sql);
    echo "<script type='text/javascript'>alert('Updated!');</script>";
    header("refresh:5;url=s.php");
}
$resetPos = "";
$resetReg = "";
$resetRoll = "";
$resetemail = "";
if (isset($_POST['reset'])) {

    $rm = false;
    $mrd = false;
    $menu = false;
    $cww = false;
    $dpsection = false;
$foodSection = false;
$invent = false;
    $pay = false;
    $c = false;
    $reset = true;
    $sql = "select * from users, payments where users.id = payments.id and users.id = $uid;";
    $res = $conn->query($sql);
    if(!($res))
    {
        $sql = "select * from users where users.id = $uid;";
        $res = $conn->query($sql);  
    }
    $row = $res->fetch_assoc();
    $resetPos = "Student";
    $resetReg = $row['reg'];
    $resetRoll = $row['roll'];
    $resetemail = $row['email'];
    if ($row['usertype'] == 'w')
        $resetDesig = "Warden";
    else {
        $resetDesig = "Chief Warden";
    }
}

$fsdata = "";
$sql = "select * from items;";
        $fsdata = $conn->query($sql);
        




if (isset($_POST['breakfast'])) {
$bf = $_POST['breakfast'];

$l = $_POST['lunch'];
$d = $_POST['dinner'];

$sn = $_POST['snacks'];
$dd = $_POST['day'];
$sql = "update timetable set breakfast = \"$bf\", lunch = \"$l\", dinner = \"$d\", snacks = \"$sn\" where day = \"$dd\";";

$conn->query($sql);
    $fin = false;
    $menu = true;
    $r = false;
    $dpsection = false;
$foodSection = false;
$invent = false;
    $statss = false;
    $don = false;
    $provision = false;

}


if (isset($_POST['killme'])) {
$killid = ($_POST['killme']);


$sql = "delete from items where id = $killid;";
$conn->query($sql);
    echo "<script type='text/javascript'>alert('Deleted!');</script>";
    header("refresh:5;url=s.php");
    $sql = "select * from items;";
        $fsdata = $conn->query($sql);
    
    $fin = false;
    $menu = false;
    $r = false;
    $dpsection = false;
$foodSection = true;
$invent = false;
    $statss = false;
    $don = false;
    $provision = false;
}

if (isset($_POST['itemName'])) {
$itemname = ($_POST['itemName']);
$quant = ($_POST['quant']);
$units = ($_POST['units']);

$sql = "insert into items (name, total, units) values ('$itemname', $quant, '$units');";
$conn->query($sql);
    echo "<script type='text/javascript'>alert('Added!');</script>";
    header("refresh:5;url=s.php");
    $sql = "select * from items;";
        $fsdata = $conn->query($sql);
    
    $fin = false;
    $menu = false;
    $r = false;
    $dpsection = false;
$foodSection = true;
$invent = false;
    $statss = false;
    $don = false;
    $provision = false;
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

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <script src="./js/student/s.js"></script>

</head>

<body style=" overflow:scroll;">
    <div class="alert alert-success" role="alert">
        <img src="<?php echo $dp; ?>" style = "height: 50px; width: 50px;"> &nbsp; &nbsp; Welcome to NIT AP MMS Dashboard <?php echo $_SESSION['name']; ?>
        <form action="s.php" method="post" style="float: right;">
            <input type="hidden" value="m" name="logout">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>


    <div id="slide-panel">
        <a href="#" class="btn btn-primary" id="opener">
            <i class="glyphicon glyphicon-align-justify"></i>
        </a>


        <form action="s.php" method="post">
            <input type="hidden" value="sdfd" name="addeating">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Add today's status</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="menu">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Show Menu</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="t" name="rm">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Rate Today's Meal</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="t" name="mrd">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Mess Admin Details</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="cww">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Chief Warden and Warden</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="t" name="p">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Payment Details</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="c" name="comp">
            <button type="submit" class="btn btn-primary" style="width: 100%;">File Complaint</button>
        </form>
        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="reset">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
        </form>
                <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="dp">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Profile Photo</button>
        </form>
        <?php if ($messrepo) : ?>
        <br>
        <h5> Mess Representative Funtions </h5>
                        <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="foodSection">
            <button type="submit" class="btn btn-warning" style="width: 100%;">Food Section</button>
        </form>
        
                                <br>
        <form action="s.php" method="post">
            <input type="hidden" value="m" name="inventory">
            <button type="submit" class="btn btn-warning" style="width: 100%;">Inventory Section</button>
        </form>
        <?php endif; ?>
    </div>

    <div id="content">
            <div class="container" style= "max-height: 180%; max-width: 100%; overflow: scroll;">

                <div class="container">
            <?php if ($foodSection) : ?>
            
                <h2>Food Section</h2>
                <h6>Please do your responsibilities as a mess representative by keeping this section up to date</h6>
                <hr>
                                <h5>Add raw materials which are usually used in the mess</h5>
<form action="s.php" method="post">
    <label for="formGroupExampleInput">Item Name</label>
    <input required type="text" class="form-control" id="itemName" name="itemName" placeholder="Example: rice">


    <label for="formGroupExampleInput2">Initial Quantity</label>
    <input required min = "0" type="number" class="form-control" id="quant" name= "quant" placeholder="Example: 10">

    <label for="formGroupExampleInput2">Units</label>
    <input required type="text" class="form-control" id="units" name= "units" placeholder="Example: kg">

              <button type="submit" class="btn btn-warning" style="">Add</button>
</form>

<br> <br>
<h5>All Items</h5>

        <div class="container" style= "max-height: 500px; overflow: scroll; ">

<table class="table">
  <thead>
    <tr>
      <th scope="col">Item Name</th>
      <th scope="col">Total Quantity</th>
      <th scope="col">Units</th>
       <th scope="col">Action</th>
    </tr>
  </thead>
    <tbody>

<?php while ($row = $fsdata->fetch_array()) : ?>
<tr>
      <td><p><?php echo $row['name'] ?></p></td>
      <td><p><?php echo $row['total'] ?></p></td>
      <td><p><?php echo $row['units'] ?></p></td>
      <td>
      <form action="s.php" method="post">


    <input  type="hidden" value = "<?php echo $row['id']?>" class="form-control" id="killme" name= "killme" >

      <button type="submit" class="btn btn-danger" style="">Delete</button>
</form></td>
    </tr>
<?php endwhile; ?>
  </tbody>
</table>

</div>
<h5>Food Items Transaction Section</h5>
        <div class="container" style= "max-height: 500px; overflow: scroll; ">
        
        </div>



            <?php endif; ?>
        </div>
        
        
            <div class="container">
            <?php if ($dpsection) : ?>
                <h2>Add or update your profile picture</h2>
                <img src="<?php echo $dp; ?>" class="rounded img-fluid" style="width: 100px; height: 100px;" alt="dp" />
                            <form method="POST" action="s.php" enctype="multipart/form-data">
                                    <input type="file" class="custom-file-input" id="dpupload" name="dpupload">
                                    <button type="submit" name="dpupload" class="btn btn-primary">Upload and Update</button>
                                </form>
                
            <?php endif; ?>
        </div>
        <div class="container" style= "max-height: 800px; max-width: 900px; overflow: scroll; ">

            <?php if ($menu) : ?>
                <h2 style="text-align: center;">NIT AP Mess Menu</h2>
                                        <table class="table">
  <thead>
    <tr>
      <th scope="col">Day</th>
      <th scope="col">Breakfast</th>
      <th scope="col">Lunch</th>
      <th scope="col">Snacks</th>
            <th scope="col">Dinner</th>

    </tr>
  </thead>
  <tbody>
                 <?php while ($row = $menudata->fetch_array()) : ?>

             <form action="ma.php" method="post">
    <tr>
            <td><p style= "background-color: black; color: white; text-align: center;"><?php echo $row['day'] ?></p></td>

      <td><p ><?php echo $row['breakfast'] ?></p></td>
      <td><p > <?php echo $row['lunch'] ?></p></td>
      <td><p > <?php echo $row['snacks'] ?></p></td>
      <td><p > <?php echo $row['dinner'] ?></p></td>

    </tr>
               </form>
   
                        
                        <?php endwhile; ?>
                          </tbody>
</table>

                            
             
            <?php endif; ?>

        </div>
        <div class="container">
            <?php if ($c) : ?>
                <h2 style="text-align: center;">File a complaint</h2>
                <div class="card text-center">
                    <div class="card-header">
                        File a complaint through CTS
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Mobile App</h5>
                        <p class="card-text">Please click the below link and download the apk file and file your complaint</p>
                        <a href="https://drive.google.com/file/d/1nvscqageL6RFWyvb_lyxioBgYIqvXCHQ/view" class="btn btn-primary">Download</a>
                    </div>
                    <div class="card-footer text-muted">
                        50MB
                    </div>
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
                    <form method="POST" action="s.php">
                        <div class="form-group">
                            <label for="rating">Give your rating</label>
                            <input min="1" max="5" value="3" required type="number" class="form-control" name="rating" id="rating" aria-describedby="ratingHelp">
                            <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <div class="container" style="overflow: scroll; max-height: 500px;">

            <?php if ($cww) : ?>
                <div class="list-group">

                </div>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Chief Warden and Warden Details
                    </a>
                    <?php
                    while ($row = $cwwdata->fetch_array()) :
                    ?>
                        <br>
                        <a href="#" class="list-group-item list-group-item-action"><?php if ($row['usertype'] == "w") {
                                                                                        echo "<h4>Warden</h4>";
                                                                                    } else {
                                                                                        echo "<h4>Chief Warden</h4>";
                                                                                    } ?></a>
                                                                    <br> <p style= "text-align: center;"> <img src="<?php echo $row['path']; ?>" width=100px height=100px style="border-radius: 50%; "> </p>
                        <a href="#" class="list-group-item list-group-item-action">Name: <?php echo $row['name']; ?> </a>
                        <a href="#" class="list-group-item list-group-item-action">Email: <?php echo $row['email']; ?></a>
                        <a href="#" class="list-group-item list-group-item-action">Phone Number: <?php echo $row['pno']; ?></a>
                        <a href="#" class="list-group-item list-group-item-action"> Department: <?php echo $row['department']; ?></a>
                        <a href="#" class="list-group-item list-group-item-action"> Hall: <?php echo $row['hall']; ?></a>
                        <br>


                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="container">

            <?php if ($rm) : ?>

                <?php echo "<div class='alert alert-primary' role='alert'> Date: $Date <br> Day: $Day</div>"; ?>
                <form method="POST" action="s.php">
                    <div class="form-group">
                        <label for="rating">Breakfast:</label>
                        <input min="1" max="5" value="3" required type="number" class="form-control" name="br" id="br" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <div class="form-group">
                        <label for="rating">Lunch:</label>
                        <input min="1" max="5" value="3" required type="number" class="form-control" name="lr" id="lr" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <div class="form-group">
                        <label for="rating">Snacks:</label>
                        <input min="1" max="5" value="3" required type="number" class="form-control" name="sr" id="sr" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <div class="form-group">
                        <label for="rating">Dinner</label>
                        <input min="1" max="5" value="3" required type="number" class="form-control" name="dr" id="dr" aria-describedby="ratingHelp">
                        <small id="ratingHelp" class="form-text text-muted">Please give it honestly</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form><?php endif; ?>
        </div>

        <div class="container">
            <?php if ($reset) : ?>
                <h3 style=" text-align:center;">Reset your password</h3>
                <p>Other details are handled by Admin, you can contact for any problems.</p>
                <form action="s.php" method="post">
                    <label for="exampleInputEmail1">Email: <?php echo $resetemail; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Roll Number: <?php echo $resetRoll; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Registration Number: <?php echo $resetReg; ?></label>
                    <br>
                    <label for="exampleInputEmail1">Position: <?php echo $resetPos; ?></label>
                    <br>
                    <div class="form-group">
                        <label for="exampleInputPassword1">New Password</label>
                        <input required type="password" class="form-control" name="resetPass" id="resetPass" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="container">
            <?php if ($pay) : ?>
                <?php if ($status == 'T') : ?>
                    <div class="alert alert-success" role="alert">
                    <?php endif; ?>
                    <?php if ($status == 'F') : ?>
                        <div class="alert alert-danger" role="alert">
                        <?php endif; ?>
                        <h4 class="alert-heading" style="text-align: center">Mess Payment Status</h4>
                        <?php if ($status == 'F') : ?>
                            <p>Your mess payment is pending please upload your SBI Challan below and sit back.</p>
                        <?php endif; ?>
                        <?php if ($status == 'M') : ?>
                            <div class="alert alert-primary" role="alert">
                                <p>Your challan is being processed. If this turn red again, reupload right documents any problems then please contact warden which you can find in the Chief Warden and Warden Sections</p>
                            <?php endif; ?>
                            <hr>
                            <?php if ($status == 'F') : ?>
                                <p class="mb-0">Payment is Pending</p>
                            <?php endif; ?>
                            <?php if ($status == 'T') : ?>
                                <p class="mb-0">Payment is done and verified <br> Contact your care tacker in the hostel to get your mess id card</p>
                            <?php endif; ?>
                            <?php if ($status == 'M') : ?>
                                <p class="mb-0">Payment is being reviewed</p>
                            <?php endif; ?>
                            </div>
                            <h3>Total Mess Fee: <?php echo $total_amount; ?> </h3>
                            <h4 style="color: green;">Paid Amount: <?php echo $paid_amount; ?> </h3>
                            <h4 style="color: red;">Remaining Amount: <?php echo $rem_amount; ?> </h3>
                            <?php if ($status == 'F' || $status == 'M' ) : ?>
                                
                                <form method="POST" action="s.php" enctype="multipart/form-data">
<label class="custom-file-label" for="customFile">Amount you wish to pay</label>
                                <input style="width: 150px;" type="number" min="1" max ="<?php echo $rem_amount;?>" required id="paya" name="paya">
                                <br>
<label class="custom-file-label" for="customFile">upload proof of payment</label>
                                    <input type="file" class="custom-file-input" id="fileupload" name="fileupload">


                                    <button type="submit" name="fileupload" class="btn btn-primary">Upload</button>
                                </form>
                            <?php endif; ?>
                            <h3 style="text-align: center;"> Your Submissons </h3> 
                            <table class="table table-sm table-dark">
  <thead>
    <tr>
      <th scope="col">Registration Number</th>
      <th scope="col">Date</th>
      <th scope="col">Uploaded Proof</th>
      <th scope="col">Amount Paid</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
  <?php
                    while ($row = $transactions->fetch_array()) :
                    ?>
    <tr>
      <td><?php echo $row['reg']; ?></td>
      <td><?php echo $row['kab']; ?></td>
      <td><a target="_blank" href="<?php echo $row['path'];?>"> Uploaded Proof</a></td>
            <td><?php echo $row['amount']; ?></td>
    <td><?php if( $row['status'] == 1) {echo "Approved";} if( $row['status'] == 0) {echo "In Review";} if( $row['status'] == 2) {echo "Rejected";}?></td>

    </tr>
       <?php endwhile; ?>
    
  </tbody>
</table>
                                                
                    
                        <?php endif; ?>
                        </div>




                    </div>
        </div>
        </div>
    </div>



</body>

</html>