<?php include 'header.php'; ?>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'voting_system');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $candidate_id = htmlspecialchars($_POST['candidate']);
    $candidate_name = NULL;
    $candidate_post = htmlspecialchars($_POST['candidate_post']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT name FROM candidates WHERE id = ?");
    $stmt->bind_param('i', $candidate_id);
    $stmt->execute();
    $stmt->bind_result($candidate_name);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id, candidate_name, candidate_post) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiss', $user_id, $candidate_id, $candidate_name, $candidate_post);
    $stmt->execute();
    $stmt->close();
    echo "<p>Vote cast successfully!</p>";
}

$result = $conn->query("SELECT id, name FROM candidates");
$posts = $conn->query("SELECT DISTINCT post FROM candidates");

?>
<form method="post" action="vote.php">
    <label for="candidate_post">Choose post:</label>
    <select id="candidate_post" name="candidate_post" required>
        <?php while ($row = $posts->fetch_assoc()) : ?>
            <option value="<?= htmlspecialchars($row['post']) ?>"><a href="javascript:void(0);" onclick="fetchCandidate(<?php echo htmlspecialchars($row['post']); ?>)"><?= htmlspecialchars($row['post']) ?></a>
            </option>
        <?php endwhile; ?>
    </select><br>
    <label for="candidate">Choose a candidate:</label>
    <select id="candidate" name="candidate" required>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <option value=""></option>
        <?php endwhile; ?>
    </select><br>
    <input type="submit" value="Vote">
</form>

<!-- dynamic update using AJAX -->
<script>
    function fetchCandidate() {
        document.addEventListener('DOMContentLoaded', function() {
            var candidatePostSelect = document.getElementById('candidate_post');
            var candidateSelect = document.getElementById('candidate');

            candidatePostSelect.addEventListener('change', function() {
                var post = candidatePostSelect.value;

                if (post) {
                    fetch('fetch_candidates.php?post=' + post)
                        .then(response => response.json())
                        .then(data => {
                            candidateSelect.innerHTML = '<option value=""></option>';
                            data.forEach(candidate => {
                                var option = document.createElement('option');
                                option.value = candidate.id;
                                option.textContent = candidate.name;
                                candidateSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    candidateSelect.innerHTML = "<option value=''>can't fetch, won't fetch</option>";
                }
            });
        });
    }
</script>
<?php include 'footer.php'; ?>