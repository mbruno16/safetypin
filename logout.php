<?php
include('./classes/DB.php');
include('./classes/Login.php');

ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);
/**
if (!Login::isLoggedIn()) {
        die("Not logged in.");
}

if (isset($_POST['confirm'])) {

        if (isset($_POST['alldevices'])) {

                DB::query('DELETE FROM login_tokens WHERE user_id=:userid', array(':userid'=>Login::isLoggedIn()));
                unset($_COOKIE['SNID']); //added to delete cookies
                unset($_COOKIE['SNID_']);
                setcookie('SNID', '', time() - 3600, '/');
                setcookie('SNID_', '', time() - 3600, '/');

        } else {
                if (isset($_COOKIE['SNID'])) {
                        DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
                }
                unset($_COOKIE['SNID']); //added to delete cookies
                unset($_COOKIE['SNID_']);
                setcookie('SNID', '', time() - 3600, '/');
                setcookie('SNID_', '', time() - 3600, '/');
                        
        }
                
}
*/
if (!Login::isLoggedIn()) {
        die("Not logged in.");
}
if (isset($_COOKIE['SNID'])) {
        DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));
}
        unset($_COOKIE['SNID']); //added to delete cookies
        unset($_COOKIE['SNID_']);
        setcookie('SNID', '', time() - 3600, '/');
        setcookie('SNID_', '', time() - 3600, '/');

if (!Login::isLoggedIn()) {
        header('location:login.html');
}
?>
<!--
<h1>Logout of your Account?</h1>
<p>Are you sure you'd like to logout?</p>
<form action="logout.php" method="post">
        <input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices?<br />
        <input type="submit" name="confirm" value="Confirm">
</form>
-->