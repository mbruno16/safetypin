<?php
include('./classes/DB.php');
include('./classes/Login.php');
ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);
$tokenIsValid = False;
if (Login::isLoggedIn()) {

        if (isset($_POST['changepassword'])) {

                $oldpassword = $_POST['oldpassword'];
                $newpassword = $_POST['newpassword'];
                $newpasswordrepeat = $_POST['newpasswordrepeat'];
                $userid = Login::isLoggedIn();

                if (password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['password'])) {

                        if ($newpassword == $newpasswordrepeat) {

                                if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {

                                        DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
                                        echo '<script>alert("Password changed successfully!")</script>';
                                        echo '<script>window.location.replace("https://safety-pin.ga/login.html")</script>';
                                }

                        } else {
                                echo '<script>alert("Passwords don\'t match!")</script>';
                        }

                } else {
                        echo '<script>alert("Incorrect old password!")</script>';
                }

        }

} else {
        if (isset($_GET['token'])) {
        $token = $_GET['token'];
        if (DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))) {
                $userid = DB::query('SELECT user_id FROM password_tokens WHERE token=:token', array(':token'=>sha1($token)))[0]['user_id'];
                $tokenIsValid = True;
                if (isset($_POST['changepassword'])) {

                        $newpassword = $_POST['newpassword'];
                        $newpasswordrepeat = $_POST['newpasswordrepeat'];

                                if ($newpassword == $newpasswordrepeat) {

                                        if (strlen($newpassword) >= 6 && strlen($newpassword) <= 60) {

                                                DB::query('UPDATE users SET password=:newpassword WHERE id=:userid', array(':newpassword'=>password_hash($newpassword, PASSWORD_BCRYPT), ':userid'=>$userid));
                                                echo '<script>alert("Password changed successfully!")</script>';
                                                DB::query('DELETE FROM password_tokens WHERE user_id=:userid', array(':userid'=>$userid));
                                                echo '<script>window.location.replace("https://safety-pin.ga/login.html")</script>';
                                        }

                                } else {
                                        echo '<script>alert("Passwords don\'t match!")</script>';
                                }

                        }


        } else {
                die('Token invalid');
        }
} else {
        die('Not logged in');
}
}
?>
<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fdc405"/>
    <title>Change Password | Safety Pin</title>
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
        <form action="<?php if (!$tokenIsValid) { echo 'change-password.php'; } else { echo 'change-password.php?token='.$token.''; } ?>" method="post">
                <div class="illustration"><img src="assets/img/login-logo.png" alt="Safety Pin"></div>
                <?php if (!$tokenIsValid) { echo '<div class="form-group"><input type="password" class="form-control" name="oldpassword" value="" placeholder="Current Password"><p/></div>'; } ?>
                <div class="form-group">
                    <input type="password" class="form-control" name="newpassword" value="" placeholder="New Password"><p/>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="newpasswordrepeat" value="" placeholder="Repeat Password"><p/>
                </div>
                <div class="form-group">
                    <input  class="btn btn-primary btn-block" type="submit" name="changepassword" value="Change Password">
                </div>
        </form>
    </center>
</div>
   
<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
