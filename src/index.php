<?php
require_once 'functions.php';

// Session for temporary storage
session_start();

$feedback = '';
$step = 'input_email';

// Handle email submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Submit email
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['pending_email'] = $email;
            $_SESSION['verification_code'] = $code;
            sendVerificationEmail($email, $code);
            $feedback = "Verification code sent to your email.";
            $step = 'input_code';
        } else {
            $feedback = "Invalid email format.";
        }
    }

    // Step 2: Submit verification code
    if (isset($_POST['verification_code'])) {
        $enteredCode = trim($_POST['verification_code']);
        if (isset($_SESSION['verification_code']) && $enteredCode === $_SESSION['verification_code']) {
            registerEmail($_SESSION['pending_email']);
            $feedback = "Email successfully registered!";
            unset($_SESSION['verification_code']);
            unset($_SESSION['pending_email']);
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
    <title>Email Registration</title>
</head>

<body>
    <h2>Register for GitHub Timeline Updates</h2>
    <p><?php echo htmlspecialchars($feedback); ?></p>

    <!-- Always show both forms as per the rules -->
    <?php if (!isset($_SESSION['pending_email'])) : ?>
        <form method="POST">
            <label for="email">Enter Email:</label><br>
            <input type="email" name="email" required><br><br>
            <button id="submit-email" type="submit">Submit</button>
        </form>

    <?php else : ?>

        <form method="POST">
            <label for="verification_code">Enter Verification Code:</label><br>
            <input type="text" name="verification_code" maxlength="6" required><br><br>
            <button id="submit-verification" type="submit">Verify</button>
        </form>
    <?php endif ?>
</body>

</html>