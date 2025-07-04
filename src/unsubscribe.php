<?php
require_once 'functions.php';
session_start();

$feedback = '';
$step = 'input_email';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Email input
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $file = __DIR__ . '/registered_emails.txt';
        $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (in_array($email, $emails)) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_email'] = $email;
            $_SESSION['unsubscribe_code'] = $code;
            sendUnsubscribeVerificationEmail($email, $code);
            $feedback = "Verification code sent to your email.";
            $step = 'input_code';
        } else {
            $feedback = "Email is not subscribed.";
        }
    }

    // Step 2: Code input
    if (isset($_POST['verification_code'])) {
        $enteredCode = trim($_POST['verification_code']);
        if (isset($_SESSION['unsubscribe_code']) && $enteredCode === $_SESSION['unsubscribe_code']) {
            unsubscribeEmail($_SESSION['unsubscribe_email']);
            $feedback = "Email successfully unsubscribed!";
            unset($_SESSION['unsubscribe_code']);
            unset($_SESSION['unsubscribe_email']);
            $step = 'completed';
        } else {
            $feedback = "Incorrect verification code.";
            $step = 'input_code';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
</head>
<body>
    <h2>Unsubscribe from GitHub Updates</h2>
    <p><?php echo htmlspecialchars($feedback); ?></p>

    <form method="POST">
        <label for="email">Enter your email to unsubscribe:</label><br>
        <input type="email" name="email" required><br><br>
        <button id="submit-unsubscribe" type="submit">Submit</button>
    </form>

    <br><hr><br>

    <form method="POST">
        <label for="verification_code">Enter verification code:</label><br>
        <input type="text" name="verification_code" maxlength="6" required><br><br>
        <button id="submit-unsubscribe-code" type="submit">Verify</button>
    </form>
</body>
</html>
