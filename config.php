<?php
$conn = new mysqli("localhost", "root", "", "website_tin_tuc");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Mail settings (optional) - you can set these in your environment or edit below.
// Recommended: set real values in env variables (or replace getenv() fallback below).
//
// Mailtrap example (from your screenshot):
// Host: sandbox.smtp.mailtrap.io
// Ports: 25, 465, 587 or 2525 (2525 is commonly used)
// Username: 44f74b52c8f2e1
// Password: (your full Mailtrap password; in the screenshot it's masked)
// Auth: PLAIN, LOGIN and CRAM-MD5 supported
// TLS: Optional (STARTTLS supported on all ports)

$MAIL_HOST = getenv('MAIL_HOST') ?: 'sandbox.smtp.mailtrap.io';
// Allow MAIL_PORT env to be a number (e.g. 2525). Default to 2525.
$MAIL_PORT = (int)(getenv('MAIL_PORT') ?: 2525);
$MAIL_USERNAME = getenv('MAIL_USERNAME') ?: '44f74b52c8f2e1';
$MAIL_PASSWORD = getenv('MAIL_PASSWORD') ?: '108dc0ff9ecf50'; // replace with the full Mailtrap password (do not commit real creds)
$MAIL_FROM = getenv('MAIL_FROM') ?: 'no-reply@yourdomain.test';
$MAIL_FROM_NAME = getenv('MAIL_FROM_NAME') ?: 'DemoTinTuc';
$MAIL_SECURE = getenv('MAIL_SECURE') ?: ''; // Mailtrap typically doesn't require 'ssl' or 'tls' (leave empty or 'tls')

// Expose mail settings array for easy include
$mailConfig = [
    'host' => $MAIL_HOST,
    'port' => $MAIL_PORT,
    'username' => $MAIL_USERNAME,
    'password' => $MAIL_PASSWORD,
    'from' => $MAIL_FROM,
    'from_name' => $MAIL_FROM_NAME,
    'secure' => $MAIL_SECURE,
    // Additional helpers for debugging or tools
    'smtp_auth' => true,
    'alt_ports' => [25, 465, 587, 2525],
];

// View count threshold (seconds): when the same visitor revisits the same post,
// a new view will only be counted if the last view timestamp for that post
// is older than this value. Default: 300 seconds (5 minutes).
$VIEW_COUNT_THRESHOLD_SECONDS = (int)(getenv('VIEW_COUNT_THRESHOLD_SECONDS') ?: 300);
// Enable debug logging for view-count checks. Set env VIEW_COUNT_DEBUG=1 to enable.
$VIEW_COUNT_DEBUG = (bool)((int)(getenv('VIEW_COUNT_DEBUG') ?: 0));
// Enable or disable view counting (set to 0 to disable). Default: disabled per user request.
$VIEW_COUNT_ENABLED = (bool)((int)(getenv('VIEW_COUNT_ENABLED') ?: 1));
?>
