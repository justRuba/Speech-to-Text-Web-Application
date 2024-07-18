# Speech-to-Text-Web-Application
This project is a web application that converts speech to text using the Web Speech API. It allows users to record their speech, view the transcription, and save it to a MySQL database.

## Features

- Speech Recognition: Record speech and see it converted to text in real-time.
- Language Selection: Choose between English and Arabic for speech recognition.
- Save Transcriptions: Save the transcribed text to a database.
- View Transcriptions: View all saved transcriptions in a list format.

## Technologies Used

- HTML/CSS: For the front-end user interface.
- JavaScript: This is used to handle speech recognition and user interactions.
- PHP: For server-side logic and database interactions.
- MySQL: For storing transcriptions.

## Files Overview

### index.html

The main HTML file for the application, containing:

- Speech Recognition Interface: Includes buttons to start and stop recording.
- Language Selector: Allows users to choose the language for speech recognition.
- Transcription Display: Shows the recognized text.
- Save Button: Saves the transcription to the database

```plaintext
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speech to Text</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Speech to Text</h1>
        <select id="language-select">
            <option value="en-US">English</option>
            <option value="ar">Arabic</option>
        </select>
        <div class="buttons">
            <button id="start-record-btn">Start Recording</button>
            <button id="stop-record-btn" disabled>Stop Recording</button>
        </div>
        <p id="status"></p>
        <p id="transcription"></p>
        <form id="transcription-form" action="transcribe.php" method="POST">
            <input type="hidden" name="transcription" id="transcription-input">
            <button type="submit" id="save-btn" disabled>Save Transcription</button>
        </form>
        <a href="view_transcriptions.php">View Transcriptions</a>
    </div>

    <script>
        const startRecordBtn = document.getElementById('start-record-btn');
        const stopRecordBtn = document.getElementById('stop-record-btn');
        const status = document.getElementById('status');
        const transcription = document.getElementById('transcription');
        const transcriptionInput = document.getElementById('transcription-input');
        const saveBtn = document.getElementById('save-btn');
        const languageSelect = document.getElementById('language-select');

        let recognition;
        let selectedLanguage = languageSelect.value;

        if (!('webkitSpeechRecognition' in window)) {
            status.innerHTML = 'Speech recognition is not supported in this browser.';
        } else {
            recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;

            recognition.lang = selectedLanguage;

            recognition.onstart = () => {
                status.innerHTML = 'Recording...';
                startRecordBtn.disabled = true;
                stopRecordBtn.disabled = false;
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                transcription.innerHTML = transcript;
                transcriptionInput.value = transcript;
                saveBtn.disabled = false;
            };

            recognition.onerror = (event) => {
                status.innerHTML = 'Error occurred in recognition: ' + event.error;
            };

            recognition.onend = () => {
                status.innerHTML = 'Recording stopped.';
                startRecordBtn.disabled = false;
                stopRecordBtn.disabled = true;
            };
        }

        startRecordBtn.onclick = () => {
            selectedLanguage = languageSelect.value;
            recognition.lang = selectedLanguage;
            recognition.start();
        };

        stopRecordBtn.onclick = () => {
            recognition.stop();
        };
    </script>
</body>
</html>
```

### transcribe.php

The PHP script that handles saving the transcription to the MySQL database. It:

- Connects to the database.
- Insert the transcription into the transcriptions table.
- Provides a link to view saved transcriptions.

```plaintext
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
```

### view_transcriptions.php

The PHP script displays all saved transcriptions in a table format. It:

- Connects to the database.
- Retrieves and displays all transcriptions sorted by time.

```plaintext
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
```

### styles.css

The CSS file for styling the application. It includes:

- General Styling: For layout, typography, and colors.
- Button Styles: These are for hover effects and button appearance.

```plaintext
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f0f0f0; /* Light gray background */
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    max-width: 600px;
    width: 100%;
    background-color: #ffffff; /* White background */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center; /* Center content */
}

.buttons {
    margin-bottom: 20px; /* Space below buttons */
    display: flex;
    justify-content: center;
}

h1 {
    color: #333;
    text-transform: uppercase; /* Uppercase headings */
    margin-bottom: 20px;
}

button {
    margin: 10px;
    padding: 12px 24px;
    font-size: 16px;
    background-color: #4CAF50; /* Green button */
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #45a049; /* Darker green on hover */
}

select {
    padding: 10px;
    font-size: 16px;
    margin-bottom: 20px;
    border-radius: 4px;
    border: 1px solid #ddd;
    background-color: #fff;
    cursor: pointer;
}

p {
    margin: 10px 0;
    color: #333;
}

a {
    display: block; /* Display links as block elements */
    margin-top: 20px;
    text-align: center; /* Center align links */
    text-decoration: none;
    color: #007BFF;
}

a:hover {
    text-decoration: underline;
}
```

## The Web Page

![WhatsApp Image 2024-07-18 at 23 10 53_0cdec9cc](https://github.com/user-attachments/assets/e67112d6-0636-431f-884f-63eee2fb45e7)

## The SAVED TRANSCRIPTIONS

![WhatsApp Image 2024-07-18 at 23 12 40_62cdf7c1](https://github.com/user-attachments/assets/008dfca2-88ed-495b-bd1b-1d08499471f4)

