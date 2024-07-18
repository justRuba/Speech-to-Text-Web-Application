<?php
if (isset($_POST['transcription'])) {
    $transcription = $_POST['transcription'];

    $conn = new mysqli('localhost', 'root', '', 'speech_to_text'); // Update credentials here

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO transcriptions (text, transcription_time) VALUES (?, NOW())");
    $stmt->bind_param('s', $transcription);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo "Transcription saved successfully. <a href='view_transcriptions.php'>View transcriptions</a>";
} else {
    echo 'No transcription received.';
}
?>
