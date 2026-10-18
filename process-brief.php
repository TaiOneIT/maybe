<?php
// process-brief.php
// Securely handles incoming project brief submissions and saves them to MySQL using PDO.
// Requires environment variables: DB_HOST, DB_NAME, DB_USER, DB_PASS

session_start();

// Helper: redirect with status and message
function redirectWith($status, $message = '') {
    $params = http_build_query(['status' => $status, 'message' => $message]);
    header("Location: index.php?$params");
    exit;
}

// Validate POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWith('error', 'Invalid request method.');
}

// Validate CSRF token
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    redirectWith('error', 'Invalid CSRF token. Please reload the page and try again.');
}

// Collect and sanitize inputs
function get_trim($key, $maxLen = 1000) {
    $v = $_POST[$key] ?? '';
    $v = trim($v);
    if (strlen($v) > $maxLen) {
        $v = substr($v, 0, $maxLen);
    }
    return $v;
}

$name = get_trim('name', 150);
$email = get_trim('email', 200);
$company = get_trim('company', 150);
$phone = get_trim('phone', 60);
$budget = get_trim('budget', 60);
$timeline = get_trim('timeline', 60);
$message = get_trim('message', 5000);
$services = $_POST['services'] ?? [];
if (!is_array($services)) $services = [];

// Basic validation
if (empty($name) || empty($email)) {
    redirectWith('error', 'Name and email are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWith('error', 'Invalid email address.');
}

// Limit sizes to avoid abuse
if (strlen($message) > 5000) {
    redirectWith('error', 'Message too long.');
}

// Prepare data for DB
$services_json = json_encode(array_values(array_map('trim', $services)), JSON_UNESCAPED_UNICODE);
$ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
$created_at = date('Y-m-d H:i:s');

// Load DB config from environment
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_name = getenv('DB_NAME') ?: 'sitegate';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';

// Build DSN
$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => false,
    ]);
} catch (Exception $ex) {
    // Do not leak DB details; log on server if needed.
    redirectWith('error', 'Database connection failed. Contact administrator.');
}

try {
    $sql = "INSERT INTO inquiries (name, email, company, phone, services, budget, timeline, message, ip_address, created_at, status)
            VALUES (:name, :email, :company, :phone, :services, :budget, :timeline, :message, :ip_address, :created_at, :status)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':company' => $company ?: null,
        ':phone' => $phone ?: null,
        ':services' => $services_json,
        ':budget' => $budget ?: null,
        ':timeline' => $timeline ?: null,
        ':message' => $message ?: null,
        ':ip_address' => $ip_address,
        ':created_at' => $created_at,
        ':status' => 'new'
    ]);
} catch (Exception $ex) {
    // Optionally log $ex->getMessage() to a file for debugging.
    redirectWith('error', 'Failed to save your inquiry. Please try again.');
}

// Optionally: send a notification email to admin (uncomment and configure if mail is available)
// $adminEmail = 'hello@sitegate.rw';
// $subject = 'New Project Inquiry';
// $body = "Name: {$name}\nEmail: {$email}\nCompany: {$company}\nPhone: {$phone}\nServices: {$services_json}\nMessage:\n{$message}\n";
// mail($adminEmail, $subject, $body);

// Success: clear CSRF to prevent accidental duplicate resubmission
unset($_SESSION['csrf_token']);
redirectWith('success', 'Your inquiry was submitted successfully. Thank you.');
?>
