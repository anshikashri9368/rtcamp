<?php

require_once 'your_script.php'; // Or copy the needed functions

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'], $_POST['code'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $verified = false;

    $pendingFile = __DIR__ . '/pending_verifications.txt';
    if (!file_exists($pendingFile)) {
        die("No verification requests found.");
    }

    $lines = file($pendingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $remainingLines = [];

    foreach ($lines as $line) {
        list($storedEmail, $storedCode, $timestamp) = explode('|', $line);
        if ($storedEmail === $email && $storedCode === $code) {
            $verified = true;
            registerEmail($email);
            continue; // Skip writing this verified line back
        }
        $remainingLines[] = $line;
    }

    // Save updated lines (removes verified entry)
    file_put_contents($pendingFile, implode(PHP_EOL, $remainingLines) . PHP_EOL);

    echo $verified
        ? "✅ $email verified and subscribed!"
        : "❌ Invalid verification code!";
}
?>
