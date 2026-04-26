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
            $stmt = $pdo->prepare("SELECT f.*, u.name as farmer_name FROM field f JOIN `user` u ON f.farmer_id = u.user_id WHERE f.field_id = ?");
            $stmt->execute([$id]);
            $field = $stmt->fetch();
            if (!$field) jsonResponse(['error' => 'Field not found'], 404);
            $s = $pdo->prepare("SELECT COUNT(*) FROM sensor WHERE field_id = ?"); $s->execute([$id]);
            $field['sensor_count'] = $s->fetchColumn();
            $s = $pdo->prepare("SELECT * FROM crop WHERE field_id = ?"); $s->execute([$id]);
            $field['crops'] = $s->fetchAll();
            jsonResponse($field);
        } else {
            $farmerId = $_GET['farmer_id'] ?? null;
            if ($farmerId) {
                $stmt = $pdo->prepare("SELECT f.*, u.name as farmer_name FROM field f JOIN `user` u ON f.farmer_id = u.user_id WHERE f.farmer_id = ? ORDER BY f.field_id");
                $stmt->execute([$farmerId]);
            } else {
                $stmt = $pdo->query("SELECT f.*, u.name as farmer_name FROM field f JOIN `user` u ON f.farmer_id = u.user_id ORDER BY f.field_id");
            }
            $fields = $stmt->fetchAll();
            foreach ($fields as &$f) {
                $s = $pdo->prepare("SELECT COUNT(*) FROM sensor WHERE field_id = ?"); $s->execute([$f['field_id']]);
                $f['sensor_count'] = $s->fetchColumn();
            }
            jsonResponse($fields);
        }
        break;

    case 'POST':
        $data = getJsonBody();
        if (empty($data['location']) || empty($data['size']) || empty($data['irrigation_type']) || empty($data['farmer_id'])) {
            jsonResponse(['error' => 'location, size, irrigation_type, and farmer_id are required'], 400);
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO field (location, size, irrigation_type, farmer_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['location'], $data['size'], $data['irrigation_type'], $data['farmer_id']]);
            jsonResponse(['message' => 'Field created', 'field_id' => $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Failed to create field'], 500);
        }
        break;

    case 'PUT':
        $data = getJsonBody();
        $id = $data['field_id'] ?? $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'field_id is required'], 400);
        $fields = []; $params = [];
        foreach (['location', 'size', 'irrigation_type', 'farmer_id'] as $f) {
            if (isset($data[$f])) { $fields[] = "`$f` = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) jsonResponse(['error' => 'No fields to update'], 400);
        $params[] = $id;
        $pdo->prepare("UPDATE field SET " . implode(', ', $fields) . " WHERE field_id = ?")->execute($params);
        jsonResponse(['message' => 'Field updated']);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'id parameter is required'], 400);
        $stmt = $pdo->prepare("DELETE FROM field WHERE field_id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(['error' => 'Field not found'], 404);
        jsonResponse(['message' => 'Field deleted']);
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
