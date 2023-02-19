<?php
include('./classes/DB.php');
include('./classes/Mail.php');
ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);
error_reporting(0);
ini_set('display_errors', 0);

if (isset($_POST['resetpassword'])) {

        $cstrong = True;
        $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
        $email = $_POST['email'];
        $user_id = DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id'];
        if (DB::query('SELECT id FROM users WHERE email=:email', array(':email'=>$email))[0]['id']){
        DB::query('INSERT INTO password_tokens VALUES(NULL, :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));
        Mail::sendMail('Forgot Password', "Click this link to change your password: https://safety-pin.ga/change-password.php?token=$token</a>", $email);
        echo '<script>alert("Email Sent!")</script>';
        }
        else {
            echo '<script>alert("This email does not exist!")</script>';
        }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdc405"/>
    <title>Forgot Password | Safety Pin</title>
    <link rel="shortcut icon" type="image/png" href="HOMEICON/48.png"/>
    <link rel="apple-touch-icon" href="HOMEICON/192.png">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="manifest" href="/manifest.json">
    <style type="text/css">
    /* width */
    ::-webkit-scrollbar {
      width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      background: #f1f1f1; 
    }
     
    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #888; 
      border-radius: 50px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
    </style>
</head>

    <div class="login-clean">
            <center>
            <form action="forgot-password.php" method="post">
                <h2 class="sr-only">Forgot Password</h2>
                <div class="illustration"><img src="assets/img/login-logo.png" alt="Safety Pin"></div> <!--added icon-->
                <div class="form-group">
                    <div class="error-response"> </div>
                </div>
                <div class="form-group">
                    <input class="form-control" type="text" name="email" value="" placeholder="Email Address" required/>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary btn-block" type="submit" name="resetpassword"> Reset Password</button><br><a href="login.html" class="forgot">Already got an account? Click here!</a>
                </div>
            </form>
            </center>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
