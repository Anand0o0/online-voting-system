<?php include 'header.php'; ?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];
        $registration_number = filter_input(INPUT_POST, 'registration_number', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $role = $_POST['role'];

        if (!$email) {
            die("Invalid email address.");
        }

        if (!preg_match('/@ptuniv\.edu\.in$/', $email)) {
            die("Invalid email address. Must be a ptuniv.edu.in email.");
        }

        if (!preg_match('/^\d{2}[A-Za-z]{2}\d{4}$/', $registration_number)) {
            die("Invalid registration number. Format: 2 digits, 2 letters, 4 digits.");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_token = rand(100000, 999999); // Generate a random verification token

        $conn = new mysqli('localhost', 'root', '', 'voting_system');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check for duplicate email or registration number
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR registration_number = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('ss', $email, $registration_number);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            die("Email address or registration number already registered.");
        }

        $stmt = $conn->prepare("INSERT INTO users (username, password, registration_number, email, role, verification_token) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('ssssss', $username, $hashed_password, $registration_number, $email, $role, $verification_token);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        $stmt->close();
        $conn->close();

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'anand2020143@gmail.com'; // Your Gmail address
            $mail->Password = 'app password'; // Your Gmail app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('your-email@gmail.com', 'Online Voting System'); // Replace with your email and name
            $mail->addAddress($email, $username);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body = "Your verification code is: $verification_token";

            $mail->send();
            echo 'Verification email has been sent.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        // Redirect to verification form
        header("Location: verify.php?email=$email");
        exit;
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
        <option value="assistant">Assistant Class Representative</option>
        <option value="admin">Class Representative</option>
    </select><br>

    <input type="submit" name="register" value="Register">
</form>
<?php include 'footer.php'; ?>
