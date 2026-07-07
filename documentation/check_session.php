<?php
// Script untuk mengecek status session
session_start();

echo "<h2>Session Status Check</h2>";
echo "<pre>";

echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . (session_status() == PHP_SESSION_ACTIVE ? "Active" : "Not Active") . "\n\n";

echo "Session Data:\n";
if (empty($_SESSION)) {
    echo "No session data found\n";
} else {
    foreach ($_SESSION as $key => $value) {
        echo "$key: " . print_r($value, true) . "\n";
    }
}

echo "\nCookies:\n";
if (empty($_COOKIE)) {
    echo "No cookies found\n";
} else {
    foreach ($_COOKIE as $key => $value) {
        echo "$key: $value\n";
    }
}

echo "\nServer Variables:\n";
echo "HTTP_USER_AGENT: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Not set') . "\n";
echo "REMOTE_ADDR: " . ($_SERVER['REMOTE_ADDR'] ?? 'Not set') . "\n";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "\n";

echo "</pre>";
?>
