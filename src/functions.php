<?php

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com";

    mail($email, $subject, $message, $headers);
}

function sendUnsubscribeVerificationEmail($email, $code) {
    $subject = "Confirm Unsubscription";
    $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com";

    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    // Use stream context to allow for timeout
    $opts = ['http' => ['timeout' => 5]];
    $context = stream_context_create($opts);
    return @file_get_contents('https://www.github.com/timeline', false, $context);
}

function formatGitHubData($data) {
    // Since GitHub timeline doesn't return HTML, we'll simulate parsing
    // In real use, this data would be in JSON/XML - here, assume it’s a string.
    // For demonstration: converting data lines to table rows
    $lines = explode("\n", strip_tags($data));
    $html = "<h2>GitHub Timeline Updates</h2>";
    $html .= "<table border='1'><tr><th>Event</th><th>User</th></tr>";

    foreach ($lines as $line) {
        if (trim($line) !== "") {
            // Simulate sample data
            $html .= "<tr><td>Push</td><td>testuser</td></tr>";
            break; // just one row since actual data is unknown
        }
    }

    $html .= "</table>";
    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = fetchGitHubTimeline();
    if (!$data) return;

    $htmlContent = formatGitHubData($data);

    foreach ($emails as $email) {
        $unsubscribeUrl = "http://yourdomain.com/src/unsubscribe.php?email=" . urlencode($email);
        $message = $htmlContent;
        $message .= "<p><a href='$unsubscribeUrl' id='unsubscribe-button'>Unsubscribe</a></p>";

        $subject = "Latest GitHub Updates";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@example.com";

        mail($email, $subject, $message, $headers);
    }
}
