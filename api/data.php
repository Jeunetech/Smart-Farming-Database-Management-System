<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');
$pdo    = getDB();
$method = getMethod();

switch ($method) {
    case 'GET':
        $id   = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare("SELECT d.*, f.location as field_location, s.type as sensor_type FROM data_table d JOIN field f ON d.field_id = f.field_id LEFT JOIN sensor s ON d.sensor_id = s.sensor_id WHERE d.data_id = ?");
            $stmt->execute([$id]);
            $record = $stmt->fetch();
            if (!$record) jsonResponse(['error' => 'Data record not found'], 404);
            foreach (['soil_data', 'weather_data', 'irrigation_data', 'equipment_data'] as $sub) {
                $s = $pdo->prepare("SELECT * FROM `$sub` WHERE data_id = ?"); $s->execute([$id]);
                $subData = $s->fetch();
                if ($subData) { $record['subtype'] = str_replace('_data', '', $sub); $record = array_merge($record, $subData); break; }
            }
            jsonResponse($record);
        } else {
            $where = []; $params = [];
            if (!empty($_GET['field_id'])) { $where[] = "d.field_id = ?"; $params[] = $_GET['field_id']; }
            if (!empty($_GET['sensor_id'])) { $where[] = "d.sensor_id = ?"; $params[] = $_GET['sensor_id']; }
            if (!empty($_GET['from'])) { $where[] = "d.`timestamp` >= ?"; $params[] = $_GET['from']; }
            if (!empty($_GET['to'])) { $where[] = "d.`timestamp` <= ?"; $params[] = $_GET['to']; }

            if ($type === 'soil') {
                $sql = "SELECT d.*, sd.ph_level, sd.moisture, sd.nutrient_levels, sd.sample_date, f.location as field_location FROM data_table d INNER JOIN soil_data sd ON d.data_id = sd.data_id JOIN field f ON d.field_id = f.field_id";
            } elseif ($type === 'weather') {
                $sql = "SELECT d.*, wd.temperature, wd.humidity, wd.rainfall, wd.wind_speed, f.location as field_location FROM data_table d INNER JOIN weather_data wd ON d.data_id = wd.data_id JOIN field f ON d.field_id = f.field_id";
            } elseif ($type === 'irrigation') {
                $sql = "SELECT d.*, id.water_amount, id.irrigation_type as irr_type, id.duration, f.location as field_location FROM data_table d INNER JOIN irrigation_data id ON d.data_id = id.data_id JOIN field f ON d.field_id = f.field_id";
            } elseif ($type === 'equipment') {
                $sql = "SELECT d.*, ed.type as equip_type, ed.usage_hours, ed.maintenance_date, ed.status as equip_status, f.location as field_location FROM data_table d INNER JOIN equipment_data ed ON d.data_id = ed.data_id JOIN field f ON d.field_id = f.field_id";
            } else {
                $sql = "SELECT d.*, f.location as field_location, s.type as sensor_type FROM data_table d JOIN field f ON d.field_id = f.field_id LEFT JOIN sensor s ON d.sensor_id = s.sensor_id";
            }
            if ($where) $sql .= " WHERE " . implode(' AND ', $where);
            $sql .= " ORDER BY d.`timestamp` DESC";
            if (!empty($_GET['limit'])) { $sql .= " LIMIT " . intval($_GET['limit']); }
            $stmt = $pdo->prepare($sql); $stmt->execute($params);
            jsonResponse($stmt->fetchAll());
        }
        break;

    case 'POST':
        $data = getJsonBody();
        if (empty($data['value']) || empty($data['unit']) || empty($data['field_id'])) {
            jsonResponse(['error' => 'value, unit, and field_id are required'], 400);
        }
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO data_table (`timestamp`, value, unit, field_id, sensor_id) VALUES (NOW(), ?, ?, ?, ?)");
            $stmt->execute([$data['value'], $data['unit'], $data['field_id'], $data['sensor_id'] ?? null]);
            $dataId = $pdo->lastInsertId();
            $type = $data['type'] ?? null;
            if ($type === 'soil') {
                $pdo->prepare("INSERT INTO soil_data (data_id, ph_level, moisture, nutrient_levels, sample_date) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$dataId, $data['ph_level'], $data['moisture'], $data['nutrient_levels'] ?? null, $data['sample_date'] ?? date('Y-m-d')]);
            } elseif ($type === 'weather') {
                $pdo->prepare("INSERT INTO weather_data (data_id, temperature, humidity, rainfall, wind_speed) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$dataId, $data['temperature'], $data['humidity'], $data['rainfall'], $data['wind_speed']]);
            } elseif ($type === 'irrigation') {
                $pdo->prepare("INSERT INTO irrigation_data (data_id, water_amount, irrigation_type, duration) VALUES (?, ?, ?, ?)")
                    ->execute([$dataId, $data['water_amount'], $data['irrigation_type'], $data['duration']]);
            } elseif ($type === 'equipment') {
                $pdo->prepare("INSERT INTO equipment_data (data_id, type, usage_hours, maintenance_date, status) VALUES (?, ?, ?, ?, ?)")
                    ->execute([$dataId, $data['equip_type'], $data['usage_hours'], $data['maintenance_date'] ?? null, $data['equip_status'] ?? 'operational']);
            }
            $pdo->commit();
            jsonResponse(['message' => 'Data record created', 'data_id' => $dataId], 201);
        } catch (PDOException $e) {
            $pdo->rollBack();
            jsonResponse(['error' => 'Failed to create data record: ' . $e->getMessage()], 500);
        }
        break;

    case 'PUT':
        $data = getJsonBody();
        $id = $data['data_id'] ?? $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'data_id is required'], 400);
        $fields = []; $params = [];
        foreach (['value', 'unit', 'field_id', 'sensor_id'] as $f) {
            if (isset($data[$f])) { $fields[] = "`$f` = ?"; $params[] = $data[$f]; }
        }
        if (!empty($fields)) {
            $params[] = $id;
            $pdo->prepare("UPDATE data_table SET " . implode(', ', $fields) . " WHERE data_id = ?")->execute($params);
        }
        jsonResponse(['message' => 'Data record updated']);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) jsonResponse(['error' => 'id parameter is required'], 400);
        $stmt = $pdo->prepare("DELETE FROM data_table WHERE data_id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(['error' => 'Data record not found'], 404);
        jsonResponse(['message' => 'Data record deleted']);
        break;

    default:
        jsonResponse(['error' => 'Method not allowed'], 405);
}
