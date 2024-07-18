<?php
$conn = new mysqli('localhost', 'root', '', 'speech_to_text'); // Update credentials here

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, text, transcription_time FROM transcriptions ORDER BY transcription_time DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcriptions</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Saved Transcriptions</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Text</th>
            <th>Transcription Time</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['text']}</td>
                        <td>{$row['transcription_time']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No transcriptions found.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
    <a href="index.html">Go back</a>
</body>
</html>
