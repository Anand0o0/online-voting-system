<?php include 'header.php'; ?>
<?php
session_start();
ob_start();

require 'vendor/phpmailer/phpmailer/PHPMailer-6.9.1/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/PHPMailer-6.9.1/src/Exception.php';
require 'vendor/phpmailer/phpmailer/PHPMailer-6.9.1/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $registration_number = htmlspecialchars($_POST['registration_number']);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $role = htmlspecialchars($_POST['role']);
    $verification_code = rand(100000, 999999);
    $is_verified = 0;

    if (!preg_match('/@ptuniv\.edu\.in$/', $email)) {
        die("Invalid email address. Must be a ptuniv.edu.in email.");
    }

    if (!preg_match('/^\d{2}[A-Z]{2}\d{4}$/', $registration_number)) {
        die("Invalid registration number. Format: 2 digits, 2 letters, 4 digits.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli('localhost', 'root', '', 'voting_system');
    $stmt = $conn->prepare("INSERT INTO users (username, password, registration_number, email, role, is_verified, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssss', $username, $hashed_password, $registration_number, $email, $role, $is_verified, $verification_code);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo "<p>Registration successful! Please check your email for the verification code.</p>";
    if (isset($_POST['submitRegister']) && $is_verified === 0) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth   = true;
            $mail->Username   = 'vijethayc@gmail.com';
            $mail->Password   = 'wckobphfuzilxdns';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('vijethayc@gmail.com', 'Vijetha Y C');
            $mail->addAddress($email, $username);

            //$mail->addAttachment('/uploads/profile-picture-f67r1m9y562wdtin.jpg', 'GIRL');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'ONLINE VOTING SYSTEM: Email Verification';
            $mail->Body    = "Hi $username,<br><br>Your verification code is: <b>$verification_code</b><br><br>Thank you!";
            $mail->AltBody = "Hi $username,\n\nYour verification code is: $verification_code\n\nThank you!";

            $mail->SMTPDebug = 2;
            // $mail->send();
            // echo 'Verification email has been sent.';

            if ($mail->send()) {
                echo 'Verification email has been sent.';
                ob_end_clean(); // Clean output buffer before header()
                if ($role === 'admin') {
                    header("Location: admin_login.php");
                } else {
                    header("Location: login.php");
                }
                exit(0);
            } else {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                ob_end_clean();
                header("Location: {$_SERVER["HTTP_REFERER"]}");
                exit(0);
            }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>
<form id="registration-form" method="post" action="register.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <label for="registration_number">Registration Number:</label>
    <input type="text" id="registration_number" name="registration_number" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="voter">Voter</option>
        <option value="acr">Assistant Class Representative</option>
        <option value="cr">Class Representative</option>
        <option value="admin">Admin</option>
    </select><br>

    <input type="submit" value="Register" name="submitRegister">
</form>
<?php include 'footer.php'; ?>