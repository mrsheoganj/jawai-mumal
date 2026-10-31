<?php
/**
 * Jawai Mumal - Contact & Inquiry Handler
 */
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        $_SESSION['toast'] = "Thank you, $name. Your inquiry has been transmitted to our General Manager.";
    } else {
        $_SESSION['toast'] = "Please fill in all required fields.";
    }
}

header("Location: index.php#contact");
exit;
