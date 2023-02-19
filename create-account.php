<?php
include('classes/DB.php');
include('classes/Mail.php');

if (isset($_POST['createaccount'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $cnumber = $_POST['cnumber'];

        if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {

                if (strlen($username) >= 3 && strlen($username) <= 32) {

                        if (preg_match('/[a-zA-Z0-9_]+/', $username)) {

                                if (strlen($password) >= 6 && strlen($password) <= 60) {

                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                                if (!DB::query('SELECT email FROM users WHERE email=:email', array(':email'=>$email))) {

                                        DB::query('INSERT INTO users VALUES (\'\', :username, :password, :fullname, :email,  :cnumber, \'0\', \'\')', array(':fullname'=>$fullname, ':username'=>$username, ':password'=>password_hash($password, PASSWORD_BCRYPT), ':fullname'=>$fullname, ':email'=>$email, ':cnumber'=>$cnumber));
                                        Mail::sendMail('Welcome to SAFETY PIN!', 'Your account has been created!', $email);
                                        echo "Success!";
                                } else {
                                        echo "<span class='form-error'>Email in use!</span>";
                                }
                        } else {
                                        echo  "<span class='form-error'>Invalid email!</span>";
                                }
                        } else {
                                echo  "<span class='form-error'>Invalid password!</span>";
                        }
                        } else {
                                echo  "<span class='form-error'>Invalid username.</span>";
                        }
                } else {
                        echo  "<span class='form-error'>Invalid username.</span>";
                }

        } else {
                echo "<span class='form-error'>User already exists!</span>";
        }
}
?>
