<?php include 'header.php'; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $registration_number = filter_input(INPUT_POST, 'registration_number', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $role = $_POST['role'];

    if (!preg_match('/@ptuniv\.edu\.in$/', $email)) {
        die("Invalid email address. Must be a ptuniv.edu.in email.");
    }

    if (!preg_match('/^\d{2}[A-Za-z]{3}\d{4}$/', $registration_number)) {
        die("Invalid registration number. Format: 2 digits, 3 letters, 4 digits.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli('localhost', 'root', '', 'voting_system');
    $stmt = $conn->prepare("INSERT INTO users (username, password, registration_number, email, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $username, $hashed_password, $registration_number, $email, $role);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo "<p>Registration successful!</p>";
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

    <input type="submit" value="Register">
</form>
<?php include 'footer.php'; ?>
