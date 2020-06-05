<?php
require_once './api/connection.php';

if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['usertype'])) {
    $email = ($_POST['email']);
    $password = ($_POST['password']);
    $usertype = ($_POST['usertype']);

    $sql = "select * from users where email = '$email' and password = '$password' and usertype = '$usertype';";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $row['id'];
        $_SESSION['usertype'] = $usertype;
        $_SESSION['name'] = $row['email'];
        if ($usertype == 'a') {
        header('Location: a.php');
        }
        else if ($usertype == 'cw'){
            header('Location: w.php');
        }
        else if ($usertype == 'w'){
            header('Location: w.php');
        }
        else if ($usertype == 'ma'){
            $_SESSION['data'] = array();
            header('Location: ma.php');
            
        }
        else if ($usertype == 's'){
            header('Location: s.php');
        }
    } else {
        //error
        echo "<div class='alert alert-danger' role='alert'> Sorry input credentials does not match with our DB </div>";
    }
    
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>NIT Andhra Pradesh Mess Management System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <script src="js/libs/jquery-1.11.3.min.js"></script>
        <script src="js/libs/dynamics.min.js"></script>
        <script src="js/libs/angular.min.js"></script>
        <script src="js/libs/angular-route.min.js"></script>
        <script src="js/libs/angular-resource.min.js"></script>
        <script src="js/libs/angular-animate.min.js"></script>
        <script src="js/libs/angular-aria.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>

       
    </head>
    <body>

        <div class="container">
            <div class="row" style= "margin-top: 100px">
                    <div style="position: relative">
                        <h5><img src="https://www.nitandhra.ac.in/main/images/logo.png" style="height: 100px; width:115px; margin-right: auto; margin-left: auto;" /> NIT Andhra Pradesh Mess Management System</h5>
                </div>
            </div>
            <div class="row" style="margin-top: 50px">
            <div class = "container">

                <form method="POST" action="login.php">
                        <div class="form-group">
                             <label for="email">Email address</label>
                                <input type="email" class="form-control" name = "email" id="email" aria-describedby="emailHelp">
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                            <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name= "password" class="form-control" id="password">
                        </div>
                            <div class="radio">
                                        <label>
                                            <input type="radio" name="usertype" value="a" checked>
                                            Admin
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="usertype" value="cw">
                                            Chief Warden
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="usertype" value="w">
                                            Warden
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="usertype" value="ma">
                                            Mess Admin
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="usertype" value="s">
                                            Student
                                        </label>
                                    </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                </form>
</div>
                    
                        
                        
                    </div>
                </div>
            </div>
        </div>





    </body>
</html>