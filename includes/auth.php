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
    static $roleCache = [];
    if (isset($roleCache[$userId])) return $roleCache[$userId];

    $stmt = $pdo->prepare("
        SELECT 
            CASE 
                WHEN EXISTS(SELECT 1 FROM farmer WHERE user_id = ?) THEN 'farmer'
                WHEN EXISTS(SELECT 1 FROM agronomist WHERE user_id = ?) THEN 'agronomist'
                WHEN EXISTS(SELECT 1 FROM technician WHERE user_id = ?) THEN 'technician'
                WHEN EXISTS(SELECT 1 FROM dba WHERE user_id = ?) THEN 'dba'
                ELSE 'unknown'
            END as role
    ");
    $stmt->execute([$userId, $userId, $userId, $userId]);
    $role = $stmt->fetchColumn() ?: 'unknown';
    
    $roleCache[$userId] = $role;
    return $role;
}

function getAllUserRoles(PDO $pdo): array {
    $stmt = $pdo->query("
        SELECT user_id, 
            CASE 
                WHEN user_id IN (SELECT user_id FROM farmer) THEN 'farmer'
                WHEN user_id IN (SELECT user_id FROM agronomist) THEN 'agronomist'
                WHEN user_id IN (SELECT user_id FROM technician) THEN 'technician'
                WHEN user_id IN (SELECT user_id FROM dba) THEN 'dba'
                ELSE 'unknown'
            END as role
        FROM `user`
    ");
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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
