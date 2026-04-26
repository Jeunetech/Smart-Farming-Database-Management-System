<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
$pdo    = getDB();
$method = getMethod();

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT user_id, name, email, phone_number, permissions_level, experience_level FROM `user` WHERE user_id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            if (!$user) jsonResponse(['error' => 'User not found'], 404);
            $user['role'] = getUserRole($user['user_id'], $pdo);
            if ($user['role'] === 'agronomist') {
                $s = $pdo->prepare("SELECT specialization FROM agronomist WHERE user_id = ?");
                $s->execute([$id]); $user['specialization'] = $s->fetchColumn();
            } elseif ($user['role'] === 'technician') {
                $s = $pdo->prepare("SELECT specialization, field_id FROM technician WHERE user_id = ?");
                $s->execute([$id]); $extra = $s->fetch(); $user = array_merge($user, $extra ?: []);
            } elseif ($user['role'] === 'dba') {
                $s = $pdo->prepare("SELECT role FROM dba WHERE user_id = ?");
                $s->execute([$id]); $user['dba_role'] = $s->fetchColumn();
            }
            jsonResponse($user);
        } else {
            $stmt = $pdo->query("SELECT user_id, name, email, phone_number, permissions_level, experience_level FROM `user` ORDER BY user_id");
            $users = $stmt->fetchAll();
            foreach ($users as &$u) { $u['role'] = getUserRole($u['user_id'], $pdo); }
            jsonResponse($users);
        }
        break;

    case 'POST':
        $data = getJsonBody();
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            jsonResponse(['error' => 'Name, email, and password are required'], 400);
        }
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO `user` (name, email, password, phone_number, permissions_level, experience_level) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'], $data['email'], $data['password'],
                $data['phone_number'] ?? null,
                $data['permissions_level'] ?? 'basic',
                $data['experience_level'] ?? 'beginner'
            ]);
            $userId = $pdo->lastInsertId();
            $role = $data['role'] ?? 'farmer';
            if ($role === 'farmer') {
                $pdo->prepare("INSERT INTO farmer (user_id) VALUES (?)")->execute([$userId]);
            } elseif ($role === 'agronomist') {
                $pdo->prepare("INSERT INTO agronomist (user_id, specialization) VALUES (?, ?)")->execute([$userId, $data['specialization'] ?? 'General']);
            } elseif ($role === 'technician') {
                $pdo->prepare("INSERT INTO technician (user_id, specialization, field_id) VALUES (?, ?, ?)")->execute([$userId, $data['specialization'] ?? 'General', $data['field_id'] ?? 1]);
            } elseif ($role === 'dba') {
                $pdo->prepare("INSERT INTO dba (user_id, role) VALUES (?, ?)")->execute([$userId, $data['dba_role'] ?? 'Database Administrator']);
            }
            $pdo->commit();
            jsonResponse(['message' => 'User created', 'user_id' => $userId], 201);
        } catch (PDOException $e) {
            $pdo->rollBack();
            $code = $e->getCode() == 23000 ? 409 : 500;
            jsonResponse(['error' => $code == 409 ? 'Email or phone already exists' : 'Failed to create user'], $code);
        }
        break;

    case 'PUT':
        $data = getJsonBody();
        $id = $data['user_id'] ?? $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'user_id is required'], 400);
        $fields = []; $params = [];
        foreach (['name', 'email', 'phone_number', 'permissions_level', 'experience_level'] as $f) {
            if (isset($data[$f])) { $fields[] = "`$f` = ?"; $params[] = $data[$f]; }
        }
        if (isset($data['password']) && !empty($data['password'])) { $fields[] = "`password` = ?"; $params[] = $data['password']; }
        if (empty($fields)) jsonResponse(['error' => 'No fields to update'], 400);
        $params[] = $id;
        try {
            $pdo->prepare("UPDATE `user` SET " . implode(', ', $fields) . " WHERE user_id = ?")->execute($params);
            jsonResponse(['message' => 'User updated']);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Failed to update user'], 500);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'id parameter is required'], 400);
        $stmt = $pdo->prepare("DELETE FROM `user` WHERE user_id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(['error' => 'User not found'], 404);
        jsonResponse(['message' => 'User deleted']);
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
