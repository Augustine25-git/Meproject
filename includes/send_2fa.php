<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Or your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com'; // Your email
    $mail->Password = 'your_app_password'; // App password, not your main password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress($_SESSION['email']);

    $mail->Subject = 'Your 2FA Code';
    $mail->Body    = 'Your 2FA code is: ' . $_SESSION['2fa_code'];

    $mail->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}