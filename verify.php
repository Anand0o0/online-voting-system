<?php include 'header.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $verification_code = filter_input(INPUT_POST, 'verification_code', FILTER_SANITIZE_STRING);

    if (!$email) {
        die("Invalid email address.");
    }

    $conn = new mysqli('localhost', 'root', '', 'voting_system');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT verification_token FROM users WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($stored_verification_token);
    $stmt->fetch();
    $stmt->close();

    if ($verification_code == $stored_verification_token) {
        // Verification successful
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->close();
        echo "Email verified successfully! You will be redirected to the login page shortly.";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 3000); 
              </script>";
        exit;
    } else {
        echo "Invalid verification code.";
    }

    $conn->close();
}
?>
<form id="verification-form" method="post" action="verify.php">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="verification_code">Verification Code:</label>
    <input type="text" id="verification_code" name="verification_code" required><br>

    <input type="submit" value="Verify">
</form>
<?php include 'footer.php'; ?>
