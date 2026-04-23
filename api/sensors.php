<?php
/**
 * Sensors API — CRUD Endpoints
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
$pdo    = getDB();
$method = getMethod();

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT s.*, f.location as field_location FROM sensor s JOIN field f ON s.field_id = f.field_id WHERE s.sensor_id = ?");
            $stmt->execute([$id]);
            $sensor = $stmt->fetch();
            if (!$sensor) jsonResponse(['error' => 'Sensor not found'], 404);
            // Get recent data
            $d = $pdo->prepare("SELECT * FROM data_table WHERE sensor_id = ? ORDER BY `timestamp` DESC LIMIT 10");
            $d->execute([$id]); $sensor['recent_data'] = $d->fetchAll();
            jsonResponse($sensor);
        } else {
            $where = []; $params = [];
            if (!empty($_GET['field_id'])) { $where[] = "s.field_id = ?"; $params[] = $_GET['field_id']; }
            if (!empty($_GET['status'])) { $where[] = "s.status = ?"; $params[] = $_GET['status']; }
            $sql = "SELECT s.*, f.location as field_location FROM sensor s JOIN field f ON s.field_id = f.field_id";
            if ($where) $sql .= " WHERE " . implode(' AND ', $where);
            $sql .= " ORDER BY s.sensor_id";
            $stmt = $pdo->prepare($sql); $stmt->execute($params);
            jsonResponse($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = getJsonBody();
        if (empty($data['type']) || empty($data['field_id'])) {
            jsonResponse(['error' => 'type and field_id are required'], 400);
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO sensor (type, installation_date, last_calibration_date, status, field_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['type'],
                $data['installation_date'] ?? date('Y-m-d'),
                $data['last_calibration_date'] ?? null,
                $data['status'] ?? 'active',
                $data['field_id']
            ]);
            jsonResponse(['message' => 'Sensor created', 'sensor_id' => $pdo->lastInsertId()], 201);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Failed to create sensor'], 500);
        }
        break;

    case 'PUT':
        $data = getJsonBody();
        $id = $data['sensor_id'] ?? $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'sensor_id is required'], 400);
        $fields = []; $params = [];
        foreach (['type', 'installation_date', 'last_calibration_date', 'status', 'field_id'] as $f) {
            if (isset($data[$f])) { $fields[] = "`$f` = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) jsonResponse(['error' => 'No fields to update'], 400);
        $params[] = $id;
        $pdo->prepare("UPDATE sensor SET " . implode(', ', $fields) . " WHERE sensor_id = ?")->execute($params);
        jsonResponse(['message' => 'Sensor updated']);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'id parameter is required'], 400);
        $stmt = $pdo->prepare("DELETE FROM sensor WHERE sensor_id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(['error' => 'Sensor not found'], 404);
        jsonResponse(['message' => 'Sensor deleted']);
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
