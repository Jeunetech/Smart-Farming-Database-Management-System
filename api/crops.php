<?php
/**
 * Crops API — CRUD Endpoints
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
            $stmt = $pdo->prepare("SELECT c.*, f.location as field_location FROM crop c JOIN field f ON c.field_id = f.field_id WHERE c.crop_id = ?");
            $stmt->execute([$id]);
            $crop = $stmt->fetch();
            if (!$crop) jsonResponse(['error' => 'Crop not found'], 404);
            // Growth stages
            $s = $pdo->prepare("SELECT * FROM growth_stage WHERE crop_id = ?"); $s->execute([$id]);
            $crop['growth_stages'] = $s->fetchAll();
            // Subtype
            $g = $pdo->prepare("SELECT * FROM grain WHERE crop_id = ?"); $g->execute([$id]);
            $grain = $g->fetch();
            if ($grain) { $crop['crop_type'] = 'grain'; $crop = array_merge($crop, $grain); }
            else {
                $v = $pdo->prepare("SELECT * FROM vegetable WHERE crop_id = ?"); $v->execute([$id]);
                $veg = $v->fetch();
                if ($veg) { $crop['crop_type'] = 'vegetable'; $crop = array_merge($crop, $veg); }
            }
            jsonResponse($crop);
        } else {
            $fieldId = $_GET['field_id'] ?? null;
            if ($fieldId) {
                $stmt = $pdo->prepare("SELECT c.*, f.location as field_location FROM crop c JOIN field f ON c.field_id = f.field_id WHERE c.field_id = ? ORDER BY c.crop_id");
                $stmt->execute([$fieldId]);
            } else {
                $stmt = $pdo->query("SELECT c.*, f.location as field_location FROM crop c JOIN field f ON c.field_id = f.field_id ORDER BY c.crop_id");
            }
            jsonResponse($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = getJsonBody();
        if (empty($data['name']) || empty($data['field_id'])) {
            jsonResponse(['error' => 'name and field_id are required'], 400);
        }
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO crop (name, planting_date, yield_value, yield_unit, field_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['name'], $data['planting_date'] ?? date('Y-m-d'), $data['yield_value'] ?? null, $data['yield_unit'] ?? null, $data['field_id']]);
            $cropId = $pdo->lastInsertId();
            $pdo->commit();
            jsonResponse(['message' => 'Crop created', 'crop_id' => $cropId], 201);
        } catch (PDOException $e) {
            $pdo->rollBack();
            jsonResponse(['error' => 'Failed to create crop'], 500);
        }
        break;

    case 'PUT':
        $data = getJsonBody();
        $id = $data['crop_id'] ?? $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'crop_id is required'], 400);
        $fields = []; $params = [];
        foreach (['name', 'planting_date', 'yield_value', 'yield_unit', 'field_id'] as $f) {
            if (isset($data[$f])) { $fields[] = "`$f` = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) jsonResponse(['error' => 'No fields to update'], 400);
        $params[] = $id;
        $pdo->prepare("UPDATE crop SET " . implode(', ', $fields) . " WHERE crop_id = ?")->execute($params);
        jsonResponse(['message' => 'Crop updated']);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'id parameter is required'], 400);
        $stmt = $pdo->prepare("DELETE FROM crop WHERE crop_id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(['error' => 'Crop not found'], 404);
        jsonResponse(['message' => 'Crop deleted']);
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
