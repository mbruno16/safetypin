<?php
ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);
require_once('PHPMailer/PHPMailerAutoload.php');
class Mail {
        public static function sendMail($subject, $body, $address) {
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = '587';
                $mail->isHTML();
                $mail->Username = 'safety.pin31n@gmail.com';
                $mail->Password = 'safetypinpassword31n.';
                $mail->SetFrom('no-reply@safetypin.org');
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->AddAddress($address);

                $mail->Send();
        }
}
class notifMail {
        public static function sendMail($subject, $body) {
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = '587';
                $mail->isHTML();
                $mail->Username = 'safety.pin31n@gmail.com';
                $mail->Password = 'safetypinpassword31n.';
                $mail->SetFrom('no-reply@safetypin.org');
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->AddAddress($address);

                $mail->Send();
        }
}
?>
