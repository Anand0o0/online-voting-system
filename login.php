<?php include 'header.php' ?>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $registration_number = htmlspecialchars($_POST['registration_number']);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    // validation
    if (!preg_match('/@ptuniv\.edu\.in$/', $email)) {
        die("Invalid email address. Must be a ptuniv.edu.in email.");
    }

    if (!preg_match('/^\d{2}[A-Z]{2}\d{4}$/', $registration_number)) {
        die("Invalid registration number. Format: 2 digits, 2 letters, 4 digits.");
    }

    $conn = new mysqli('localhost', 'root', '', 'voting_system');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, password, is_verified FROM users WHERE username = ? AND email = ? AND registration_number = ?");
    $stmt->bind_param('sss', $username, $email, $registration_number);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $is_verified);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && password_verify($password, $hashed_password)) {
        if ($is_verified === 1) {
            $_SESSION['user_id'] = $id;
            setcookie('user_id', $_SESSION['user_id'], time() + 60); // Cookie expires in 1 minute
            header('Location: vote.php');
        } else {
            echo "Your email isn't verified. Please check your registered Gmail for verification code.";
            header('Location: verify.php');
        }
    } else {
        echo "<p>Invalid username, email, registration number, or password.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<form id="login-form" method="post" action="login.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <label for="registration_number">Registration Number:</label>
    <input type="text" id="registration_number" name="registration_number" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <input type="submit" value="Login">
</form>
<?php include 'footer.php' ?>