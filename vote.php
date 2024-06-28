<?php include 'header.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'voting_system');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidate_id = $_POST['candidate'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $user_id, $candidate_id);
    $stmt->execute();
    $stmt->close();
    echo "<p>Vote cast successfully!</p>";
}

$result = $conn->query("SELECT id, name FROM candidates");
?>
<form method="post" action="vote.php">
    <label for="candidate">Choose a candidate:</label>
    <select id="candidate" name="candidate" required>
        <?php while ($row = $result->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Vote">
</form>
<?php include 'footer.php'; ?>
