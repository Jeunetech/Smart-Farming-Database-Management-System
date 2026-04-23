<?php
/**
 * Analyzes API — CRUD for agronomist analysis records
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
$pdo    = getDB();
$method = getMethod();

switch ($method) {
    case 'GET':
        $agronomistId = $_GET['agronomist_id'] ?? null;
        if ($agronomistId) {
            $stmt = $pdo->prepare("SELECT a.*, d.value, d.unit, d.`timestamp`, f.location as field_location FROM analyzes a JOIN data_table d ON a.data_id = d.data_id JOIN field f ON d.field_id = f.field_id WHERE a.agronomist_id = ? ORDER BY a.analyzed_at DESC");
            $stmt->execute([$agronomistId]);
        } else {
            $stmt = $pdo->query("SELECT a.*, u.name as agronomist_name, d.value, d.unit, d.`timestamp`, f.location as field_location FROM analyzes a JOIN `user` u ON a.agronomist_id = u.user_id JOIN data_table d ON a.data_id = d.data_id JOIN field f ON d.field_id = f.field_id ORDER BY a.analyzed_at DESC");
        }
        jsonResponse($stmt->fetchAll());
        break;

    case 'POST':
        $data = getJsonBody();
        if (empty($data['agronomist_id']) || empty($data['data_id'])) {
            jsonResponse(['error' => 'agronomist_id and data_id are required'], 400);
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO analyzes (agronomist_id, data_id) VALUES (?, ?)");
            $stmt->execute([$data['agronomist_id'], $data['data_id']]);
            jsonResponse(['message' => 'Analysis record created'], 201);
        } catch (PDOException $e) {
            $code = $e->getCode() == 23000 ? 409 : 500;
            jsonResponse(['error' => $code == 409 ? 'Analysis already exists' : 'Failed to create analysis'], $code);
        }
        break;

    case 'DELETE':
        $agronomistId = $_GET['agronomist_id'] ?? null;
        $dataId = $_GET['data_id'] ?? null;
        if (!$agronomistId || !$dataId) jsonResponse(['error' => 'agronomist_id and data_id are required'], 400);
        $stmt = $pdo->prepare("DELETE FROM analyzes WHERE agronomist_id = ? AND data_id = ?");
        $stmt->execute([$agronomistId, $dataId]);
        if ($stmt->rowCount() === 0) jsonResponse(['error' => 'Analysis not found'], 404);
        jsonResponse(['message' => 'Analysis deleted']);
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
