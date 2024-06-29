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

$posts = $conn->query("SELECT DISTINCT post FROM candidates");

?>
<form method="post" action="vote.php">
    <label for="candidate_post">Choose post:</label>
    <select id="post_select" name="candidate_post" onchange="fetchCandidates(this.value)" required>
        <option value="">Select Post</option>
        <?php while ($row = $posts->fetch_assoc()) : ?>
            <option value="<?= htmlspecialchars($row['post']) ?>"><?= htmlspecialchars($row['post']) ?></option>
        <?php endwhile; ?>
    </select><br>

    <div id="candidate_select"></div>

    <input type="submit" value="Vote">
</form>
<button type="button" onclick="window.location.href = 'results.php'">Results</button>

<!-- AJAX -->
<script>
    function fetchCandidates(post) {
        var candidateSelectDiv = document.getElementById('candidate_select');
        var candidateSelect = document.createElement('select');
        candidateSelect.name = 'candidate';
        candidateSelect.id = 'candidate';
        candidateSelect.required = true;

        if (post) {
            fetch('fetch_candidates.php?post=' + encodeURIComponent(post))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    data.forEach(candidate => {
                    var candidateDiv = document.createElement('div');
                        candidateDiv.className = 'candidate';

                        var candidateOption = document.createElement('input');
                        candidateOption.type = 'radio';
                        candidateOption.name = 'candidate';
                        candidateOption.value = candidate.id;
                        candidateOption.required = true;

                        var candidateLabel = document.createElement('label');
                        candidateLabel.textContent = candidate.name;

                        var candidateImage = document.createElement('img');
                        candidateImage.src = candidate.image_path;
                        candidateImage.alt = candidate.name;
                        candidateImage.width = 100;

                        candidateDiv.appendChild(candidateOption);
                        candidateDiv.appendChild(candidateLabel);
                        candidateDiv.appendChild(candidateImage);
                        candidateSelectDiv.appendChild(candidateDiv);
                    });
                })
                .catch(error => console.error('Error:', error));
        } else {
            candidateSelect.innerHTML = '<option value="">Select Post first</option>';
            candidateSelectDiv.replaceChild(candidateSelect, candidateSelectDiv.firstChild);
        }
    }
</script>

<?php include 'footer.php'; ?>
