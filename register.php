<?php
/**
 * Registration Page — Smart Farming IoT
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . getDashboardUrl($_SESSION['user_role']));
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $phone      = trim($_POST['phone_number'] ?? '') ?: null;
    $role       = trim($_POST['role'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = 'Please fill in all required fields.';
    } elseif (!in_array($role, ['farmer', 'agronomist', 'technician'])) {
        $error = 'Invalid role selected.';
    } else {
        try {
            $pdo = getDB();
            // Check duplicate email
            $stmt = $pdo->prepare("SELECT user_id FROM `user` WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO `user` (name, email, password, phone_number, permissions_level, experience_level) VALUES (?, ?, ?, ?, 'basic', 'beginner')");
                $stmt->execute([$name, $email, $password, $phone]);
                $userId = $pdo->lastInsertId();

                if ($role === 'farmer') {
                    $pdo->prepare("INSERT INTO farmer (user_id) VALUES (?)")->execute([$userId]);
                } elseif ($role === 'agronomist') {
                    $spec = $specialization ?: 'General';
                    $pdo->prepare("INSERT INTO agronomist (user_id, specialization) VALUES (?, ?)")->execute([$userId, $spec]);
                } elseif ($role === 'technician') {
                    $spec = $specialization ?: 'General';
                    // Default field_id=1 for new technicians
                    $pdo->prepare("INSERT INTO technician (user_id, specialization, field_id) VALUES (?, ?, 1)")->execute([$userId, $spec]);
                }
                $pdo->commit();
                $success = 'Account created successfully! You can now sign in.';
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Farming IoT — Register</title>
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
                <div class="brand-icon"><i class="fas fa-seedling"></i></div>
                <h1>Create Account</h1>
                <p>Join the Smart Farming platform</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.2);border-radius:var(--radius-sm);padding:10px 14px;color:var(--accent);font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="reg-name">Full Name *</label>
                    <input class="form-control" type="text" id="reg-name" name="name" placeholder="Enter your full name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="reg-email">Email Address *</label>
                    <input class="form-control" type="email" id="reg-email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="reg-password">Password *</label>
                    <input class="form-control" type="password" id="reg-password" name="password" placeholder="Create a password" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="reg-phone">Phone Number</label>
                        <input class="form-control" type="text" id="reg-phone" name="phone_number" placeholder="Optional" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="reg-role">Role *</label>
                        <select class="form-control" id="reg-role" name="role" required>
                            <option value="">Select role...</option>
                            <option value="farmer" <?= ($_POST['role'] ?? '') === 'farmer' ? 'selected' : '' ?>>Farmer</option>
                            <option value="agronomist" <?= ($_POST['role'] ?? '') === 'agronomist' ? 'selected' : '' ?>>Agronomist</option>
                            <option value="technician" <?= ($_POST['role'] ?? '') === 'technician' ? 'selected' : '' ?>>Technician</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="spec-group" style="display:none">
                    <label class="form-label" for="reg-spec">Specialization</label>
                    <input class="form-control" type="text" id="reg-spec" name="specialization" placeholder="e.g. Soil Analysis, Sensor Maintenance" value="<?= htmlspecialchars($_POST['specialization'] ?? '') ?>">
                </div>
                <button class="btn btn-primary" type="submit" id="register-submit">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            <div class="login-footer">Already have an account? <a href="/index.php">Sign in</a></div>
        </div>
    </div>
    <script>
        document.getElementById('reg-role').addEventListener('change', function() {
            document.getElementById('spec-group').style.display = (this.value === 'agronomist' || this.value === 'technician') ? 'block' : 'none';
        });
    </script>
</body>
</html>
