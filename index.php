<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . getDashboardUrl($_SESSION['user_role']));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare("SELECT * FROM `user` WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && $user['password'] === $password) {
                $role = getUserRole($user['user_id'], $pdo);
                loginUser($user, $role);
                header('Location: ' . getDashboardUrl($role));
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Farming IoT — Login to your intelligent agricultural management dashboard.">
    <title>Smart Farming IoT — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h1>Smart Farming IoT</h1>
                <p>Intelligent Agricultural Management System</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="login-email">Email Address</label>
                    <input class="form-control" type="email" id="login-email" name="email"
                           placeholder="Enter your email" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="login-password">Password</label>
                    <input class="form-control" type="password" id="login-password" name="password"
                           placeholder="Enter your password" required>
                </div>
                <button class="btn btn-primary" type="submit" id="login-submit">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="login-footer">
                Don't have an account? <a href="/register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
