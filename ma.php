<?php
require_once './api/connection.php';
if (!isset($_SESSION)) {
    session_start();
}
if (!$_SESSION['loggedin'] || $_SESSION['usertype'] != 'ma') {
    header("Location: login.php");
}
$Date = date("Y/m/d");
$Day = date("l");
$MID = $_SESSION['id'];

$sql = "SELECT * FROM users where id = $MID;";
$resff = $conn->query($sql);
$row23 = $resff->fetch_assoc();
$dp = $row23['path'];
$menu = false;
$fin = true;
$dpsection = false;
$r = false;
$provision = false;
$don = false;
$statss = false;
if (isset($_POST['logout'])) {

    $_SESSION['loggedin'] = false;
    header("Location: login.php");
}
$menudata = "";
$sql = "SELECT * FROM timetable;";
$menudata = $conn->query($sql);
if (isset($_POST['umenu'])) 
{
    $menu = true;
    $r = false;
    $fin = false;
    $dpsection = false;

    $statss = false;
    $don = false;
    $provision = false;

    $sql = "SELECT * FROM timetable";
    $menudata = $conn->query($sql);
}

if (isset($_POST['fileupload'])) 
{
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
                $filedestination = './uploads/mess/' . $filenewname;
                move_uploaded_file($filetmpname, $filedestination);
                $sql = "insert into timetable (path) values ('$filedestination');";
                $conn->query($sql);
                header("Location: ma.php");
            } else {
                echo "<script type='text/javascript'>alert('Upload a file less than 5MB');</script>";
                header("refresh:5;url=ma.php");
            }
        } else {
            echo "<script type='text/javascript'>alert('There is some problem');</script>";
            header("refresh:5;url=ma.php");
        }
    } else {
        echo "<script type='text/javascript'>alert('You cannot upload this file.');</script>";
        header("refresh:5;url=ma.php");
    }
}

if (isset($_POST['ratelu'])) {
    $r = true;
    $menu = false;
    $statss = false;
    $dpsection = false;

    $provision = false;

    $don = false;

    $fin = false;
}
$eating = 0;
$noteating = 0;
$d = 0;
$l = 0;
$b = 0;
$s = 0;
if (isset($_POST['stats'])) {
    $r = false;
    $menu = false;
    $statss = true;
    $dpsection = false;

    $provision = false;

    $don = false;

    $fin = false;
    $sql = "SELECT count(id) FROM adds where day = CURRENT_DATE() and stat = 'F';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $noteating = (int) $row['count(id)'];
    $sql = "SELECT count(id) FROM adds where day = CURRENT_DATE() and stat = 'T';";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $eating = (int) $row['count(id)'];
    $sql = "SELECT avg(breakfast) FROM mratings where day = CURRENT_DATE() and mid = $MID;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $b = (int) $row['avg(breakfast)'];
    $sql = "SELECT avg(lunch) FROM mratings where day = CURRENT_DATE() and mid = $MID;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $l = (int) $row['avg(lunch)'];
    $sql = "SELECT avg(dinner) FROM mratings where day = CURRENT_DATE() and mid = $MID;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $d = (int) $row['avg(dinner)'];
    $sql = "SELECT avg(snacks) FROM mratings where day = CURRENT_DATE() and mid = $MID;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $s = (int) $row['avg(snacks)'];
}
$inr = 0;
if (isset($_POST['addAmount'])) {
    $adding = $_POST['addAmount'];
    $reason = $_POST['reaa'];
    $sql = "SELECT * FROM finance where id = $MID;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $inr = $row['amount'];
    $inr = $inr + $adding;
    $sql = "update finance set amount = $inr where id = $MID;";
    $conn->query($sql);
    $sql = "insert into ded values ($MID, '$reason', $adding, 0, CURRENT_DATE())";
    $conn->query($sql);
}

if (isset($_POST['deduct'])) {
    $ded = $_POST['deduct'];
    $reason = $_POST['read'];
    $sql = "SELECT * FROM finance where id = $MID;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $inr = $row['amount'];
    $temp = $inr - $ded;
    if ($temp < 0) {
        echo "<script type='text/javascript'>alert('Failed!');</script>";
        header("refresh:5;url=ma.php");
    }
    $inr = $inr - $ded;
    $sql = "update finance set amount = $inr where id = $MID;";
    $conn->query($sql);
    $sql = "insert into ded values ($MID, '$reason', $ded, 1, CURRENT_DATE())";
    $conn->query($sql);
}
$logdata = "";
$sql = "select * from finance where id = $MID;";
$res = $conn->query($sql);
if (!($res && $row = $res->fetch_assoc())) {
    $sql = "insert into finance values ($MID, '0');";
    $conn->query($sql);
} else {
    $inr = $row['amount'];
}
$sql = "select * from ded where id = $MID";
$res = $conn->query($sql);
$logdata = $res;
if (isset($_POST['finance'])) {
    $fin = true;
    $r = false;
    $provision = false;
$dpsection = false;

    $menu = false;
    $statss = false;
    $don = false;

    $sql = "select * from finance where id = $MID;";
    $res = $conn->query($sql);
    if (!($res && $row = $res->fetch_assoc())) {
        $sql = "insert into finance values ($MID, '0');";
        $conn->query($sql);
    } else {
        $inr = $row['amount'];
    }
    $sql = "select * from ded where id = $MID";
    $res = $conn->query($sql);
    $logdata = $res;
}
if (isset($_POST['donate'])) {
    $fin = false;
    $r = false;
    $menu = false;
    $dpsection = false;

    $statss = false;
    $don = true;
    $provision = false;
}

if (isset($_POST['search'])) {
    $fin = true;
    $menu = false;
$dpsection = false;

    $r = false;

    $statss = false;
    $don = false;
    $provision = false;
    $sql = "SELECT * FROM finance where id = $MID;";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    $inr = $row['amount'];
    $amt = $_POST['amt'];
    $roju = $_POST['roju'];
    $reas = $_POST['res'];
    $sql = "select * from ded where id = $MID";
    if ($amt) {
        $sql = $sql . " and amt = $amt";
    }
    if ($roju) {
        $sql = $sql . " and day = '$roju'";
    }
    if ($reas) {
        $sql = $sql . " and reason = '$reas'";
    }
    $res = $conn->query($sql);
    $logdata = $res;
}
if (isset($_POST['provision'])) {
    $fin = false;
    $menu = false;
    $r = false;
    $statss = false;
    $dpsection = false;

    $don = false;
    $provision = true;
}
$all = $_SESSION['data'];
$showTable = false;
if(sizeof($all) != 1) {
$showTable = true; }
else {

$showTable = false;}

if (isset($_POST['prov'])) {
    $temp = array();
    $showTable = true;
    array_push($temp, $_POST['prov']);
    array_push($temp, $_POST['q']);
    array_push($temp, $_POST['u']);
    array_push($all, $temp);
    $_SESSION['data'] = $all;
    $fin = false;
    $menu = false;
    $r = false;
    $statss = false;
    $don = false;
    $dpsection = false;

    $provision = true;
}

if (isset($_POST['download'])) {
    $i = 0;
    $fin = false;
    $menu = false;
    $r = false;
    $dpsection = false;

    $statss = false;
    $don = false;
    $provision = true;
}


if (isset($_POST['dp'])) {
   
    $fin = false;
    $menu = false;
    $r = false;
    $dpsection = true;

    $statss = false;
    $don = false;
    $provision = false;
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
                $sql = "update users set path = '$filedestination' where id = $MID";
                $conn->query($sql);
                echo "<script type='text/javascript'>alert('Success!');</script>";
                header("refresh:5;url=ma.php");
            } else {
                echo "<script type='text/javascript'>alert('Upload a file less than 5MB');</script>";
                header("refresh:5;url=ma.php");
            }
        } else {
            echo "<script type='text/javascript'>alert('There is some problem');</script>";
            header("refresh:5;url=ma.php");
        }
    } else {
        echo "<script type='text/javascript'>alert('You cannot upload this file.');</script>";
        header("refresh:5;url=ma.php");
    }
}


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
        <form action="ma.php" method="post" style="float: right;">
            <input type="hidden" value="m" name="logout">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>


    <div id="slide-panel">
        <a href="#" class="btn btn-primary" id="opener">
            <i class="glyphicon glyphicon-align-justify"></i>
        </a>


        <form action="ma.php" method="post">
            <input type="hidden" value="m" name="umenu">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Mess Menu</button>
        </form>
        <br>
        <form action="ma.php" method="post">
            <input type="hidden" value="m" name="ratelu">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Live Rates</button>
        </form>
        <br>
        <form action="ma.php" method="post">
            <input type="hidden" value="m" name="stats">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Today's Stats</button>
        </form>
        <br>
        <form action="ma.php" method="post">
            <input type="hidden" value="m" name="finance">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Finances</button>
        </form>
        <br>
        <form action="ma.php" method="post">
            <input type="hidden" value="m" name="donate">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Donate Food</button>
        </form>
        <br>
        <form action="ma.php" method="post">
            <input type="hidden" value="m" name="provision">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Make Provision List</button>
        </form>
                        <br>
        <form action="ma.php" method="post">
            <input type="hidden" value="m" name="dp">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Profile Photo</button>
        </form>
    </div>

    <div id="content">
    
                <div class="container">
            <?php if ($dpsection) : ?>
                <h2>Add or update your profile picture</h2>
                <img src="<?php echo $dp; ?>" class="rounded img-fluid" style="width: 100px; height: 100px;" alt="dp" />
                            <form method="POST" action="ma.php" enctype="multipart/form-data">
                                    <input type="file" class="custom-file-input" id="dpupload" name="dpupload">
                                    <button type="submit" name="dpupload" class="btn btn-primary">Upload and Update</button>
                                </form>
                
            <?php endif; ?>
        </div>
        <div class="container">

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
                        <th scope="col">Action</th>

    </tr>
  </thead>
  <tbody>
                 <?php while ($row = $menudata->fetch_array()) : ?>

             <form action="ma.php" method="post">
    <tr>
            <td><textarea name="day" required rows="4" cols="20" placeholder="<?php echo $row['day'] ?>"><?php echo $row['day'] ?></textarea></td>

      <td><textarea required name="breakfast" rows="4" cols="20" placeholder="<?php echo $row['breakfast'] ?>"><?php echo $row['breakfast'] ?></textarea></td>
      <td><textarea name="lunch" required rows="4" cols="20" placeholder="<?php echo $row['lunch'] ?>"> <?php echo $row['lunch'] ?></textarea></td>
      <td><textarea name="snacks" required rows="4" cols="20" placeholder="<?php echo $row['snacks'] ?>"> <?php echo $row['snacks'] ?></textarea></td>
      <td><textarea name="dinner" required rows="4" cols="20" placeholder="<?php echo $row['dinner'] ?>"> <?php echo $row['dinner'] ?></textarea></td>
            <td> <button type="submit" class="btn btn-primary" style="">Add / Update</button> </td>

    </tr>
               </form>
   
                        
                        <?php endwhile; ?>
                          </tbody>
</table>

                            
             
            <?php endif; ?>

        </div>

        <div class="container text-center">

            <?php if ($r) : ?>
                <p>Date: <?php echo $Date; ?> &nbsp; Day: <?php echo $Day; ?></p>
                <iframe style="height: 500px; width: 400px; margin-left: auto; margin-right:auto;" src="https://market.todaypricerates.com/Eluru-vegetables-price-in-Andhra-Pradesh" name="myFrame"></iframe>
                <p style="text-align:center;"><a class="btn btn-primary" href="https://market.todaypricerates.com/Eluru-vegetables-price-in-Andhra-Pradesh" target="myFrame">Vegetables Rates</a>
                    <a class="btn btn-primary" href="https://market.todaypricerates.com/chicken-mutton-beef-fish-rate-in-Eluru" target="myFrame">Meat Rates</a>
                    <a class="btn btn-primary" href="https://market.todaypricerates.com/egg-rate-in-Eluru" target="myFrame">Egg Rates</a>
                    <a class="btn btn-primary" href="https://market.todaypricerates.com/Eluru-fruits-price-in-Andhra-Pradesh" target="myFrame">Fruits Rates</a>
                </p>


            <?php endif; ?>

        </div>

        <div class="container text-center">

            <?php if ($don) : ?>
                <p>Date: <?php echo $Date; ?> &nbsp; Day: <?php echo $Day; ?></p>
                <h3>Please contact below NGOs near Tadepalligudem to donate the leftover food.</h3>

                <iframe style="height: 500px; width: 400px; margin-left: auto; margin-right:auto;" src="https://e-clubhouse.org/sites/tadepalligudemslc/contact.php" name="myFrame"></iframe>
                <p style="text-align:center;"><a class="btn btn-primary" href="https://e-clubhouse.org/sites/tadepalligudemslc/contact.php" target="myFrame">Lions Club</a>
                    <a class="btn btn-primary" href="https://www.nofoodwaste.in/contact/" target="myFrame">NoFoodWaste.in</a>
                </p>


            <?php endif; ?>

        </div>
        <div class="container text-center">

            <?php if ($statss) : ?>
                <p>Date: <?php echo $Date; ?> &nbsp; Day: <?php echo $Day; ?></p>
                <p>These are for prediction only, might vary throughout the day</p>
                <p>Ratings are out of 5 and subjected to change</p>

                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">
                        Today's Stats
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">Number of Students going to eat:<?php echo $eating; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Number of Students not going to eat: <?php echo $noteating; ?></a>
                    <a href="#" class="list-group-item list-group-item-action">Average Rating for Today's Breakfast: <?php echo $b; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Average Rating for Today's Lunch: <?php echo $l; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Average Rating for Today's Dinner: <?php echo $d; ?> </a>
                    <a href="#" class="list-group-item list-group-item-action">Average Rating for Today's Snacks: <?php echo $s; ?> </a>
                </div>

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
                <br>
            <?php endif; ?>

        </div>

        <div class="container text-center">

            <?php if ($fin) : ?>
                <h2 class="">Finance Section</h2>
                <div class="card">
                    <h5 class="card-header">Balance</h5>
                    <div class="card-body">
                        <h5 class="card-title">INR <?php echo $inr; ?></h5>
                        <p class="card-text">All actions are recorded and displayed below</p>
                        <form action="ma.php" method="post">
                            <input required placeholder="Amount" min="1" type="number" name="addAmount">
                            <input required placeholder="Reason" type="text" name="reaa">
                            <button type="submit" class="btn btn-success" style="">Add</button>
                        </form>
                        <br>
                        <form action="ma.php" method="post">
                            <input required placeholder="Amount" min="1" type="number" name="deduct">
                            <input required placeholder="Reason" type="text" name="read">
                            <button type="submit" class="btn btn-danger" style="">Deduct</button>
                        </form>
                    </div>
                </div>
                <br>
                <div class="list-group w-100 align-items-center" style="max-width: 600px; margin: 0 auto;">

                    <form action="ma.php" method="post">
                        <input type="hidden" name="search">
                        <input placeholder="Amount" type="number" name="amt">
                        <input type="date" name="roju">
                        <input placeholder="Reason" type="text" name="res">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                    <br>
                    <br>
                    <a href="#" class="list-group-item list-group-item-action active">
                        Log
                    </a>
                </div>
                <div class="container" style="max-height: 500px;  overflow:scroll;">
                    <div class="list-group w-100 align-items-center" style="max-width: 300px; margin: 0 auto;">

                        <?php
                        while ($row = $logdata->fetch_array()) :
                        ?>

                            <br>
                            <?php
                            if ($row['ded'] == 0) :
                            ?>
                                <a href="#" class="list-group-item list-group-item-success"> Amount: <?php echo $row['amt']; ?></a>
                                <a href="#" class="list-group-item list-group-item-success"> Reason: <?php echo $row['reason']; ?></a>
                                <a href="#" class="list-group-item list-group-item-success"> Date: <?php echo $row['day']; ?></a>

                            <?php endif; ?>
                            <?php
                            if ($row['ded'] == 1) :
                            ?>
                                <a href="#" class="list-group-item list-group-item-danger"> Amount: <?php echo $row['amt']; ?></a>
                                <a href="#" class="list-group-item list-group-item-danger"> Reason: <?php echo $row['reason']; ?></a>
                                <a href="#" class="list-group-item list-group-item-danger"> Date: <?php echo $row['day']; ?></a>

                            <?php endif; ?>

                            <br>


                        <?php endwhile; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>

        <div class="container">

            <?php if ($provision) : ?>

                <?php echo "<div class='alert alert-primary' role='alert'> Date: $Date <br> Day: $Day</div>"; ?>
                <form method="POST" action="ma.php" class = "provForm">
                    <div class="form-group">
                        <label for="rating">Provision Name:</label>
                        <input required type="text" class="form-control" name="prov" id="prov" aria-describedby="ratingHelp">
                        <label for="Quantity">Quantity:</label>
                        <input required type="number" class="form-control" name="q" id="q" aria-describedby="ratingHelp">
                        <label for="">Units:</label>
                        <input required type="text" class="form-control" name="u" id="u" aria-describedby="ratingHelp">
                    </div>
                    <button type="submit" class="btn btn-success">Add</button>
                </form>
                <br>
  
            <table class="table table-striped" id= "provisions">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Units</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($showTable) : ?>
                    <?php foreach ($all as $a) : ?>


                        <tr>
                            <td><?php echo $a[0]; ?></td>
                            <td><?php echo $a[1]; ?></td>
                            <td><?php echo $a[2]; ?></td>
                        </tr>

                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>             <button id="export" class="btn btn-warning">Download</button>
<?php endif; ?>

        </div>

    </div>
    </div>

    </div>




</body>


</html>