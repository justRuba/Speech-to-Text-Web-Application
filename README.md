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
