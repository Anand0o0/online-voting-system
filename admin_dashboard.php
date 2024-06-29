<?php include 'header.php'; ?>
<?php
session_start();

//allowing only admins
if (!isset($_SESSION['admin_username']) || !$_SESSION['admin_username']) {
    header('Location: admin_login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'voting_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $post = htmlspecialchars($_POST['post']);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE candidates SET name = ?, post = ? WHERE id = ?");
        $stmt->bind_param('ssi', $name, $post, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO candidates (name, post) VALUES (?, ?)");
        $stmt->bind_param('ss', $name, $post);
    }

    if ($stmt->execute()) {
        echo "Candidate successfully " . ($id > 0 ? "updated" : "added") . ".";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo "Candidate successfully deleted.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all candidates
$result = $conn->query("SELECT * FROM candidates");
$candidates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

$conn->close();
?>

<div id="admin-dashboard" class="container">
    <h1>Manage Candidates</h1>
    <form method="post" action="admin_dashboard.php">
        <input type="hidden" name="id" id="id" value="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>
        <label for="post">Post:</label>
        <select id="post" name="post" required>
            <option value="Class Representative">Class Representative</option>
            <option value="Assistant Class Representative">Assistant Class Representative</option>
        </select><br>
        <input type="submit" value="Save">
    </form>

    <h2>Candidates List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Post</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidates as $candidate) : ?>
                <tr>
                    <td><?php echo $candidate['id']; ?></td>
                    <td><?php echo $candidate['name']; ?></td>
                    <td><?php echo $candidate['post']; ?></td>
                    <td>
                        <a href="javascript:void(0);" onclick="editCandidate(<?php echo htmlspecialchars(json_encode($candidate)); ?>)">Edit</a>
                        <a href="admin_dashboard.php?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Are you sure you want to delete this candidate?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    function editCandidate(candidate) {
        document.getElementById('id').value = candidate.id;
        document.getElementById('name').value = candidate.name;
        document.getElementById('post').value = candidate.post;
    }
</script>
<?php include 'footer.php'; ?>