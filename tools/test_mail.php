<?php
require __DIR__ . '/../vendor/autoload.php';
// Load config (this sets $mailConfig)
require __DIR__ . '/../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Recipient: CLI arg or default to mailConfig 'from'
$recipient = $argv[1] ?? ($mailConfig['from'] ?? 'test@example.test');

echo "Using SMTP host: " . ($mailConfig['host'] ?? '(none)') . PHP_EOL;
echo "Using SMTP port: " . ($mailConfig['port'] ?? '(none)') . PHP_EOL;
echo "Using username: " . ($mailConfig['username'] ?? '(none)') . PHP_EOL;

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $mailConfig['host'] ?? '';
    $mail->SMTPAuth = $mailConfig['smtp_auth'] ?? true;
    if (!empty($mailConfig['auth_type'])) {
        $mail->AuthType = $mailConfig['auth_type'];
    }
    $mail->Username = $mailConfig['username'] ?? '';
    $mail->Password = $mailConfig['password'] ?? '';

    // Secure handling
    if (!empty($mailConfig['secure']) && strtolower($mailConfig['secure']) === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->Port = (int)($mailConfig['port'] ?? 2525);
    $mail->SMTPAutoTLS = true;

    $mail->setFrom($mailConfig['from'] ?? $mailConfig['username'], $mailConfig['from_name'] ?? 'DemoTinTuc');
    $mail->addAddress($recipient);

    // Verbose debug to stdout for troubleshooting
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'echo';

    $mail->isHTML(true);
    $mail->Subject = 'Test mail from DemoTinTuc';
    $mail->Body = 'This is a test email sent at ' . date('Y-m-d H:i:s');

    echo "Sending to: $recipient\n";
    $mail->send();
    echo "Message sent successfully.\n";
} catch (Exception $e) {
    echo "Message could not be sent. PHPMailer Error: " . $mail->ErrorInfo . "\n";
    echo "Exception: " . $e->getMessage() . "\n";
}
