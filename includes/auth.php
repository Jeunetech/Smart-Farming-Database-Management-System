<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}


function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}


function requireRole(string ...$roles): void {
    requireLogin();
    $userRole = $_SESSION['user_role'] ?? '';
    if (!in_array($userRole, $roles)) {
        header('HTTP/1.1 403 Forbidden');
        echo '<h1>403 — Access Denied</h1><p>You do not have permission to view this page.</p>';
        echo '<a href="/index.php">Back to Login</a>';
        exit;
    }
}


function getCurrentUser(): array {
    return [
        'user_id'           => $_SESSION['user_id'] ?? null,
        'name'              => $_SESSION['user_name'] ?? '',
        'email'             => $_SESSION['user_email'] ?? '',
        'role'              => $_SESSION['user_role'] ?? '',
        'permissions_level' => $_SESSION['permissions_level'] ?? '',
        'experience_level'  => $_SESSION['experience_level'] ?? '',
    ];
}


function getUserRole(int $userId, PDO $pdo): string {

    $stmt = $pdo->prepare("SELECT user_id FROM farmer WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'farmer';

    $stmt = $pdo->prepare("SELECT user_id FROM agronomist WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'agronomist';

    $stmt = $pdo->prepare("SELECT user_id FROM technician WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'technician';

    $stmt = $pdo->prepare("SELECT user_id FROM dba WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'dba';

    return 'unknown';
}

function loginUser(array $user, string $role): void {
    $_SESSION['user_id']           = $user['user_id'];
    $_SESSION['user_name']         = $user['name'];
    $_SESSION['user_email']        = $user['email'];
    $_SESSION['user_role']         = $role;
    $_SESSION['permissions_level'] = $user['permissions_level'];
    $_SESSION['experience_level']  = $user['experience_level'];
}

function getDashboardUrl(string $role): string {
    $map = [
        'farmer'     => '/dashboard/farmer.php',
        'agronomist' => '/dashboard/agronomist.php',
        'technician' => '/dashboard/technician.php',
        'dba'        => '/dashboard/admin.php',
    ];
    return $map[$role] ?? '/index.php';
}
