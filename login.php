<?php include 'header.php'; ?>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'voting_system');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, username, password, is_verified FROM users WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $is_verified);
        $stmt->fetch();

        if (!$is_verified) {
            echo "<p>Your email is not verified. Please check your email for the verification link.</p>";
        } else {
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: vote.php");
                exit;
            } else {
                echo "<p>Incorrect password.</p>";
            }
        }
    } else {
        echo "<p>No account found with that email address.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
<head>Login form</head>
<form id="login-form" method="post" action="login.php">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <input type="submit" value="Login">
</form>
<?php include 'footer.php'; ?>
