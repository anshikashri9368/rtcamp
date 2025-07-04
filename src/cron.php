<?php
require_once 'functions.php';

$file = __DIR__ . '/registered_emails.txt';

if (!file_exists($file)) {
    exit("No registered users.\n");
}

$emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($emails)) {
    exit("No subscribers.\n");
}

$data = fetchGitHubTimeline();

if (!$data) {
    exit("Failed to fetch GitHub timeline.\n");
}

$html = formatGitHubData($data);

// Send email to each registered user
foreach ($emails as $email) {
    $unsubscribeLink = "http://localhost/src/unsubscribe.php?email=" . urlencode($email);

    $message = $html . '<p><a href="' . $unsubscribeLink . '" id="unsubscribe-button">Unsubscribe</a></p>';

    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    mail($email, "Latest GitHub Updates", $message, $headers);
}
