<?php
/**
 * Authentication & Session Helpers
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Require login — redirect to login page if not authenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}

/**
 * Require specific role
 */
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

/**
 * Get current user data from session
 */
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

/**
 * Determine user role by checking role sub-tables
 */
function getUserRole(int $userId, PDO $pdo): string {
    // Check farmer
    $stmt = $pdo->prepare("SELECT user_id FROM farmer WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'farmer';

    // Check agronomist
    $stmt = $pdo->prepare("SELECT user_id FROM agronomist WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'agronomist';

    // Check technician
    $stmt = $pdo->prepare("SELECT user_id FROM technician WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'technician';

    // Check DBA
    $stmt = $pdo->prepare("SELECT user_id FROM dba WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) return 'dba';

    return 'unknown';
}

/**
 * Set session data after login
 */
function loginUser(array $user, string $role): void {
    $_SESSION['user_id']           = $user['user_id'];
    $_SESSION['user_name']         = $user['name'];
    $_SESSION['user_email']        = $user['email'];
    $_SESSION['user_role']         = $role;
    $_SESSION['permissions_level'] = $user['permissions_level'];
    $_SESSION['experience_level']  = $user['experience_level'];
}

/**
 * Get dashboard URL for a role
 */
function getDashboardUrl(string $role): string {
    $map = [
        'farmer'     => '/dashboard/farmer.php',
        'agronomist' => '/dashboard/agronomist.php',
        'technician' => '/dashboard/technician.php',
        'dba'        => '/dashboard/admin.php',
    ];
    return $map[$role] ?? '/index.php';
}
