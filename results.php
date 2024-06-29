<?php include 'header.php'; ?>
<?php
$conn = new mysqli('localhost', 'root', '', 'voting_system');
$result = $conn->query("
    SELECT candidates.name, candidates.image_path, COUNT(votes.id) AS vote_count 
    FROM votes 
    JOIN candidates ON votes.candidate_id = candidates.id 
    GROUP BY votes.candidate_id
");
?>
<table>
    <thead>
        <tr>
            <th>Candidate</th>
            <th>Pictures</th>
            <th>Votes</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" width="100"></td>
                <td><?= htmlspecialchars($row['vote_count']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php include 'footer.php'; ?>
