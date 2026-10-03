<?php
require __DIR__ . '/includes/bootstrap.php';
require './configuration/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $stmt = $pdo->prepare("SELECT username, pass FROM users WHERE username = :u");
    $stmt->execute(['u' => $_POST['username'] ?? '']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $pw = $_POST['password'] ?? '';
    // Accepts hashed passwords (password_hash) and, for the legacy sample data, plain text.
    $ok = $user && (password_verify($pw, $user['pass']) || hash_equals($user['pass'], $pw));
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];
        header("Location: patients_list.php"); exit();
    }
    $_SESSION['error_message'] = 'err_login';
}
header("Location: doctors_login.php"); exit();
