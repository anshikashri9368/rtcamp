<?php
$to      = 'test@example.com';
$subject = 'Testing Mailpit';
$message = 'Hello, this is a test email sent using PHP!';
$headers = 'From: you@example.com' . "\r\n" .
           'Reply-To: you@example.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>
