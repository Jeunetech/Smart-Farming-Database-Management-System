-- --------------------------------------------------------
-- Host:                         roundhouse.proxy.rlwy.net
-- Server version:               9.4.0 - MySQL Community Server - GPL
-- Server OS:                    Linux
-- HeidiSQL Version:             12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for railway
CREATE DATABASE IF NOT EXISTS `railway` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `railway`;

-- Dumping structure for table railway.agronomist
CREATE TABLE IF NOT EXISTS `agronomist` (
  `user_id` int unsigned NOT NULL,
  `specialization` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `agronomist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.agronomist: ~3 rows (approximately)
INSERT INTO `agronomist` (`user_id`, `specialization`) VALUES
	(2, 'Soil Analysis');
INSERT INTO `agronomist` (`user_id`, `specialization`) VALUES
	(8, 'Soil analysis');
INSERT INTO `agronomist` (`user_id`, `specialization`) VALUES
	(17, 'Asu');

-- Dumping structure for table railway.analyzes
CREATE TABLE IF NOT EXISTS `analyzes` (
  `agronomist_id` int unsigned NOT NULL,
  `data_id` int unsigned NOT NULL,
  `analyzed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agronomist_id`,`data_id`),
  KEY `data_id` (`data_id`),
  CONSTRAINT `analyzes_ibfk_1` FOREIGN KEY (`agronomist_id`) REFERENCES `agronomist` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `analyzes_ibfk_2` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.analyzes: ~5 rows (approximately)
INSERT INTO `analyzes` (`agronomist_id`, `data_id`, `analyzed_at`) VALUES
	(2, 1, '2026-04-22 16:24:44');
INSERT INTO `analyzes` (`agronomist_id`, `data_id`, `analyzed_at`) VALUES
	(2, 2, '2026-04-22 16:24:44');
INSERT INTO `analyzes` (`agronomist_id`, `data_id`, `analyzed_at`) VALUES
	(2, 3, '2026-04-22 16:24:44');
INSERT INTO `analyzes` (`agronomist_id`, `data_id`, `analyzed_at`) VALUES
	(2, 4, '2026-04-22 16:24:44');
INSERT INTO `analyzes` (`agronomist_id`, `data_id`, `analyzed_at`) VALUES
	(2, 5, '2026-04-22 16:24:44');

-- Dumping structure for table railway.crop
CREATE TABLE IF NOT EXISTS `crop` (
  `crop_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `planting_date` date NOT NULL DEFAULT (curdate()),
  `yield_value` decimal(10,2) DEFAULT NULL,
  `yield_unit` varchar(20) DEFAULT NULL,
  `field_id` int unsigned NOT NULL,
  PRIMARY KEY (`crop_id`),
  KEY `field_id` (`field_id`),
  CONSTRAINT `crop_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.crop: ~6 rows (approximately)
INSERT INTO `crop` (`crop_id`, `name`, `planting_date`, `yield_value`, `yield_unit`, `field_id`) VALUES
	(1, 'Tomato', '2026-04-01', 500.00, 'kg', 1);
INSERT INTO `crop` (`crop_id`, `name`, `planting_date`, `yield_value`, `yield_unit`, `field_id`) VALUES
	(2, 'Wheat', '2026-03-15', 2.50, 'ton', 2);
INSERT INTO `crop` (`crop_id`, `name`, `planting_date`, `yield_value`, `yield_unit`, `field_id`) VALUES
	(3, 'Corn', '2026-03-20', 2.80, 'ton', 1);
INSERT INTO `crop` (`crop_id`, `name`, `planting_date`, `yield_value`, `yield_unit`, `field_id`) VALUES
	(4, 'Potato', '2026-04-05', 700.00, 'kg', 4);
INSERT INTO `crop` (`crop_id`, `name`, `planting_date`, `yield_value`, `yield_unit`, `field_id`) VALUES
	(5, 'Rice', '2026-03-10', 3.00, 'ton', 5);
INSERT INTO `crop` (`crop_id`, `name`, `planting_date`, `yield_value`, `yield_unit`, `field_id`) VALUES
	(6, 'Pepper', '2026-04-10', 450.00, 'kg', 1);

-- Dumping structure for table railway.data_table
CREATE TABLE IF NOT EXISTS `data_table` (
  `data_id` int unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `value` decimal(12,4) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `field_id` int unsigned NOT NULL,
  `sensor_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`data_id`),
  KEY `field_id` (`field_id`),
  KEY `sensor_id` (`sensor_id`),
  CONSTRAINT `data_table_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `data_table_ibfk_2` FOREIGN KEY (`sensor_id`) REFERENCES `sensor` (`sensor_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.data_table: ~8 rows (approximately)
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(1, '2026-04-22 16:23:40', 6.5000, 'pH', 1, 1);
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(2, '2026-04-22 16:23:40', 28.4000, 'Celsius', 2, 2);
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(3, '2026-04-22 16:23:40', 100.0000, 'liters', 3, 3);
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(4, '2026-04-22 16:23:40', 12.0000, 'hours', 4, 4);
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(5, '2026-04-22 16:23:40', 7.2000, 'pH', 5, 5);
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(6, '2026-04-28 15:56:57', 55.0000, 'Celsius', 6, NULL);
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(7, '2026-04-29 16:42:40', 66.0000, 'Celsius', 4, NULL);
INSERT INTO `data_table` (`data_id`, `timestamp`, `value`, `unit`, `field_id`, `sensor_id`) VALUES
	(8, '2026-04-29 17:19:59', 65.0000, 'liters', 2, NULL);

-- Dumping structure for table railway.dba
CREATE TABLE IF NOT EXISTS `dba` (
  `user_id` int unsigned NOT NULL,
  `role` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `dba_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.dba: ~3 rows (approximately)
INSERT INTO `dba` (`user_id`, `role`) VALUES
	(1, 'Database Administrator');
INSERT INTO `dba` (`user_id`, `role`) VALUES
	(4, 'Database Administrator');
INSERT INTO `dba` (`user_id`, `role`) VALUES
	(6, 'Database Administrator');

-- Dumping structure for table railway.equipment_data
CREATE TABLE IF NOT EXISTS `equipment_data` (
  `data_id` int unsigned NOT NULL,
  `type` varchar(100) NOT NULL,
  `usage_hours` decimal(8,2) NOT NULL,
  `maintenance_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'operational',
  PRIMARY KEY (`data_id`),
  CONSTRAINT `equipment_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.equipment_data: ~1 rows (approximately)
INSERT INTO `equipment_data` (`data_id`, `type`, `usage_hours`, `maintenance_date`, `status`) VALUES
	(4, 'Tractor', 12.00, '2026-04-15', 'operational');

-- Dumping structure for table railway.farmer
CREATE TABLE IF NOT EXISTS `farmer` (
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `farmer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.farmer: ~6 rows (approximately)
INSERT INTO `farmer` (`user_id`) VALUES
	(5);
INSERT INTO `farmer` (`user_id`) VALUES
	(11);
INSERT INTO `farmer` (`user_id`) VALUES
	(12);
INSERT INTO `farmer` (`user_id`) VALUES
	(14);
INSERT INTO `farmer` (`user_id`) VALUES
	(16);
INSERT INTO `farmer` (`user_id`) VALUES
	(22);

-- Dumping structure for table railway.field
CREATE TABLE IF NOT EXISTS `field` (
  `field_id` int unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `size` decimal(10,2) NOT NULL,
  `irrigation_type` varchar(100) NOT NULL,
  `farmer_id` int unsigned NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY `farmer_id` (`farmer_id`),
  CONSTRAINT `field_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmer` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.field: ~6 rows (approximately)
INSERT INTO `field` (`field_id`, `location`, `size`, `irrigation_type`, `farmer_id`) VALUES
	(1, 'North Farm', 12.53, 'drip', 5);
INSERT INTO `field` (`field_id`, `location`, `size`, `irrigation_type`, `farmer_id`) VALUES
	(2, 'East Farm', 20.00, 'sprinkler', 5);
INSERT INTO `field` (`field_id`, `location`, `size`, `irrigation_type`, `farmer_id`) VALUES
	(3, 'South Farm', 15.75, 'flood', 14);
INSERT INTO `field` (`field_id`, `location`, `size`, `irrigation_type`, `farmer_id`) VALUES
	(4, 'West Farm', 30.00, 'drip', 5);
INSERT INTO `field` (`field_id`, `location`, `size`, `irrigation_type`, `farmer_id`) VALUES
	(5, 'Central Farm', 18.25, 'sprinkler', 11);
INSERT INTO `field` (`field_id`, `location`, `size`, `irrigation_type`, `farmer_id`) VALUES
	(6, 'Famagusta', 1.00, 'manual', 12);

-- Dumping structure for function railway.GetActiveSensorCount
DELIMITER //
CREATE FUNCTION `GetActiveSensorCount`(p_field_id INT) RETURNS int
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE cnt INT;
    SELECT COUNT(*) INTO cnt
    FROM sensor
    WHERE field_id = p_field_id AND status = 'active';
    RETURN IFNULL(cnt, 0);
END//
DELIMITER ;

-- Dumping structure for procedure railway.GetAllFields
DELIMITER //
CREATE PROCEDURE `GetAllFields`()
BEGIN
    SELECT field_id, location FROM field ORDER BY field_id;
END//
DELIMITER ;

-- Dumping structure for function railway.GetAverageSoilPH
DELIMITER //
CREATE FUNCTION `GetAverageSoilPH`(p_field_id INT) RETURNS decimal(5,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE avg_ph DECIMAL(5,2);
    SELECT AVG(sd.ph_level) INTO avg_ph
    FROM soil_data sd
    JOIN data_table d ON sd.data_id = d.data_id
    WHERE d.field_id = p_field_id;
    RETURN IFNULL(avg_ph, 0.00);
END//
DELIMITER ;

-- Dumping structure for function railway.GetEquipmentMaintenanceStatus
DELIMITER //
CREATE FUNCTION `GetEquipmentMaintenanceStatus`(p_data_id INT) RETURNS varchar(20) CHARSET utf8mb4
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE last_date DATE;
    DECLARE days_since INT;
    SELECT maintenance_date INTO last_date
    FROM equipment_data WHERE data_id = p_data_id;

    IF last_date IS NULL THEN
        RETURN 'No record';
    END IF;

    SET days_since = DATEDIFF(CURDATE(), last_date);

    IF days_since > 90 THEN
        RETURN 'Overdue';
    ELSEIF days_since > 60 THEN
        RETURN 'Due soon';
    ELSE
        RETURN 'OK';
    END IF;
END//
DELIMITER ;

-- Dumping structure for procedure railway.GetFarmers
DELIMITER //
CREATE PROCEDURE `GetFarmers`()
BEGIN
    SELECT f.user_id, u.name 
    FROM farmer f 
    JOIN `user` u ON f.user_id = u.user_id;
END//
DELIMITER ;

-- Dumping structure for function railway.GetFieldCropCount
DELIMITER //
CREATE FUNCTION `GetFieldCropCount`(p_field_id INT) RETURNS int
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE cnt INT;
    SELECT COUNT(*) INTO cnt FROM crop WHERE field_id = p_field_id;
    RETURN IFNULL(cnt, 0);
END//
DELIMITER ;

-- Dumping structure for function railway.GetLatestSensorReading
DELIMITER //
CREATE FUNCTION `GetLatestSensorReading`(p_sensor_id INT) RETURNS decimal(12,4)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE latest_val DECIMAL(12,4);
    SELECT value INTO latest_val
    FROM data_table
    WHERE sensor_id = p_sensor_id
    ORDER BY `timestamp` DESC
    LIMIT 1;
    RETURN IFNULL(latest_val, 0.0000);
END//
DELIMITER ;

-- Dumping structure for function railway.GetTotalIrrigationWater
DELIMITER //
CREATE FUNCTION `GetTotalIrrigationWater`(p_field_id INT) RETURNS decimal(12,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE total DECIMAL(12,2);
    SELECT SUM(id.water_amount) INTO total
    FROM irrigation_data id
    JOIN data_table d ON id.data_id = d.data_id
    WHERE d.field_id = p_field_id;
    RETURN IFNULL(total, 0.00);
END//
DELIMITER ;

-- Dumping structure for function railway.GetTotalYield
DELIMITER //
CREATE FUNCTION `GetTotalYield`(p_field_id INT) RETURNS decimal(10,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE total DECIMAL(10,2);
    SELECT SUM(yield_value) INTO total
    FROM crop
    WHERE field_id = p_field_id;
    RETURN IFNULL(total, 0);
END//
DELIMITER ;

-- Dumping structure for procedure railway.GetUserByEmail
DELIMITER //
CREATE PROCEDURE `GetUserByEmail`(IN p_email VARCHAR(150))
BEGIN
    SELECT * FROM `user` WHERE email = p_email;
END//
DELIMITER ;

-- Dumping structure for function railway.GetUserRole
DELIMITER //
CREATE FUNCTION `GetUserRole`(p_user_id INT) RETURNS varchar(20) CHARSET utf8mb4
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE role_label VARCHAR(20);
    IF EXISTS (SELECT 1 FROM dba         WHERE user_id = p_user_id) THEN SET role_label = 'dba';
    ELSEIF EXISTS (SELECT 1 FROM agronomist  WHERE user_id = p_user_id) THEN SET role_label = 'agronomist';
    ELSEIF EXISTS (SELECT 1 FROM technician  WHERE user_id = p_user_id) THEN SET role_label = 'technician';
    ELSEIF EXISTS (SELECT 1 FROM farmer      WHERE user_id = p_user_id) THEN SET role_label = 'farmer';
    ELSE SET role_label = 'unknown';
    END IF;
    RETURN role_label;
END//
DELIMITER ;

-- Dumping structure for table railway.grain
CREATE TABLE IF NOT EXISTS `grain` (
  `crop_id` int unsigned NOT NULL,
  `growth_duration` int NOT NULL,
  `size` varchar(50) NOT NULL,
  `water_requirement` decimal(8,2) NOT NULL,
  `temperature_tolerance` varchar(50) NOT NULL,
  `harvest_method` varchar(100) NOT NULL,
  PRIMARY KEY (`crop_id`),
  CONSTRAINT `grain_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`crop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.grain: ~3 rows (approximately)
INSERT INTO `grain` (`crop_id`, `growth_duration`, `size`, `water_requirement`, `temperature_tolerance`, `harvest_method`) VALUES
	(2, 120, 'large', 5.50, 'high', 'combine');
INSERT INTO `grain` (`crop_id`, `growth_duration`, `size`, `water_requirement`, `temperature_tolerance`, `harvest_method`) VALUES
	(3, 90, 'medium', 4.00, 'medium', 'mechanical');
INSERT INTO `grain` (`crop_id`, `growth_duration`, `size`, `water_requirement`, `temperature_tolerance`, `harvest_method`) VALUES
	(5, 150, 'small', 6.00, 'high', 'manual');

-- Dumping structure for table railway.growth_stage
CREATE TABLE IF NOT EXISTS `growth_stage` (
  `stage_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `crop_id` int unsigned NOT NULL,
  PRIMARY KEY (`stage_id`),
  UNIQUE KEY `name` (`name`,`crop_id`),
  KEY `crop_id` (`crop_id`),
  CONSTRAINT `growth_stage_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`crop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.growth_stage: ~5 rows (approximately)
INSERT INTO `growth_stage` (`stage_id`, `name`, `description`, `crop_id`) VALUES
	(1, 'Seedling', 'Initial growth stage', 1);
INSERT INTO `growth_stage` (`stage_id`, `name`, `description`, `crop_id`) VALUES
	(2, 'Vegetative', 'Leaf development', 2);
INSERT INTO `growth_stage` (`stage_id`, `name`, `description`, `crop_id`) VALUES
	(3, 'Flowering', 'Flower stage', 3);
INSERT INTO `growth_stage` (`stage_id`, `name`, `description`, `crop_id`) VALUES
	(4, 'Maturity', 'Ready for harvest', 4);
INSERT INTO `growth_stage` (`stage_id`, `name`, `description`, `crop_id`) VALUES
	(5, 'Ripening', 'Final stage', 5);

-- Dumping structure for table railway.irrigation_data
CREATE TABLE IF NOT EXISTS `irrigation_data` (
  `data_id` int unsigned NOT NULL,
  `water_amount` decimal(10,2) NOT NULL,
  `irrigation_type` varchar(50) NOT NULL,
  `duration` int NOT NULL,
  PRIMARY KEY (`data_id`),
  CONSTRAINT `irrigation_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.irrigation_data: ~2 rows (approximately)
INSERT INTO `irrigation_data` (`data_id`, `water_amount`, `irrigation_type`, `duration`) VALUES
	(3, 100.00, 'drip', 30);
INSERT INTO `irrigation_data` (`data_id`, `water_amount`, `irrigation_type`, `duration`) VALUES
	(8, 65.00, 'sprinkler', 22);

-- Dumping structure for table railway.sensor
CREATE TABLE IF NOT EXISTS `sensor` (
  `sensor_id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `installation_date` date NOT NULL DEFAULT (curdate()),
  `last_calibration_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `field_id` int unsigned NOT NULL,
  PRIMARY KEY (`sensor_id`),
  KEY `field_id` (`field_id`),
  CONSTRAINT `sensor_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.sensor: ~5 rows (approximately)
INSERT INTO `sensor` (`sensor_id`, `type`, `installation_date`, `last_calibration_date`, `status`, `field_id`) VALUES
	(1, 'soil', '2026-01-10', '2026-04-01', 'active', 1);
INSERT INTO `sensor` (`sensor_id`, `type`, `installation_date`, `last_calibration_date`, `status`, `field_id`) VALUES
	(2, 'weather', '2026-01-12', '2026-04-02', 'active', 2);
INSERT INTO `sensor` (`sensor_id`, `type`, `installation_date`, `last_calibration_date`, `status`, `field_id`) VALUES
	(3, 'irrigation', '2026-01-15', '2026-04-03', 'active', 3);
INSERT INTO `sensor` (`sensor_id`, `type`, `installation_date`, `last_calibration_date`, `status`, `field_id`) VALUES
	(4, 'equipment', '2026-01-18', '2026-04-04', 'maintenance', 4);
INSERT INTO `sensor` (`sensor_id`, `type`, `installation_date`, `last_calibration_date`, `status`, `field_id`) VALUES
	(5, 'soil', '2026-01-20', '2026-04-05', 'active', 5);

-- Dumping structure for table railway.soil_data
CREATE TABLE IF NOT EXISTS `soil_data` (
  `data_id` int unsigned NOT NULL,
  `ph_level` decimal(4,2) NOT NULL,
  `moisture` decimal(5,2) NOT NULL,
  `nutrient_levels` text,
  `sample_date` date DEFAULT (curdate()),
  PRIMARY KEY (`data_id`),
  CONSTRAINT `soil_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.soil_data: ~2 rows (approximately)
INSERT INTO `soil_data` (`data_id`, `ph_level`, `moisture`, `nutrient_levels`, `sample_date`) VALUES
	(1, 6.50, 45.00, 'NPK Balanced', '2026-04-20');
INSERT INTO `soil_data` (`data_id`, `ph_level`, `moisture`, `nutrient_levels`, `sample_date`) VALUES
	(5, 7.20, 50.00, 'High Potassium', '2026-04-20');

-- Dumping structure for table railway.technician
CREATE TABLE IF NOT EXISTS `technician` (
  `user_id` int unsigned NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `field_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `field_id` (`field_id`),
  CONSTRAINT `technician_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `technician_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.technician: ~6 rows (approximately)
INSERT INTO `technician` (`user_id`, `specialization`, `field_id`) VALUES
	(3, 'Sensor Maintenance', 1);
INSERT INTO `technician` (`user_id`, `specialization`, `field_id`) VALUES
	(9, 'General', 1);
INSERT INTO `technician` (`user_id`, `specialization`, `field_id`) VALUES
	(10, 'Sensor maintenance', 1);
INSERT INTO `technician` (`user_id`, `specialization`, `field_id`) VALUES
	(18, 'Mmk', 1);
INSERT INTO `technician` (`user_id`, `specialization`, `field_id`) VALUES
	(19, 'Logistics', 1);
INSERT INTO `technician` (`user_id`, `specialization`, `field_id`) VALUES
	(23, 'Sensor maintenance', 1);

-- Dumping structure for table railway.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `permissions_level` varchar(50) NOT NULL DEFAULT 'basic',
  `experience_level` varchar(50) NOT NULL DEFAULT 'beginner',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.user: ~19 rows (approximately)
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(1, 'taraneh', 't@t', '123', 'taraneh', 'admin', 'expert');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(2, 'Alice Agro', 'alice@example.com', 'password123', '5555555552', 'standard', 'expert');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(3, 'David Tech', 'david@example.com', 'password123', '5555555553', 'standard', 'intermediate');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(4, 'Sarah DBA', 'sarah@example.com', 'password123', '5555555554', 'admin', 'expert');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(5, 'Michael Farmer', 'michael@example.com', 'password123', '5555555555', 'basic', 'intermediate');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(6, 'AUDRY jeuneTECH', 'audrymwansa6@gmail.com', 'Audrym', '5428836083', 'admin', 'expert');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(7, 'Lalo lolo', 'Landrymwansa@gmail.com', 'Lalo', '0811612953', 'basic', 'intermediate');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(8, 'Mahisa', 'mia.ahadi.nejad@gmail.com', '12345678', '0811612954', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(9, 'Lorraine Masvata', 'masvatalorraine49@gmail.com', 'Lolo@2014', '0811612956', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(10, 'Ayakoz Kanatnur', 'kanatnur0501@gmail.com', 'ayakoz0501', '5428836082', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(11, 'landry', 'audrymwansa@gmail.com', 'Audrym', '5428836081', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(12, 'Itachi', 'itachi1uchiws01@gmail.com', '12345678', '0811612958', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(14, 'Djelika kone', 'konedjelika760@gmail.com', 'gakgog-Zewzom-5vudxu', '5333876332', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(16, 'CC T HSHS', 'ysnc006@gmail.com', 'Denny123@', '6959595969', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(17, 'Cc Hshs', 'dennysyahputra2900@gmail.com', 'Denny123@', '6959595968', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(18, 'Suhhs', 'asu@gmail.com', 'Denny123@', '3837727282', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(19, 'Raymond Belinga eyenga', 'raymond.belinga@outlook.com', 'RAYm@nd25', '5469928106', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(22, 'farmer-test', 'farmer@farmer', '123', '5543332211', 'basic', 'beginner');
INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone_number`, `permissions_level`, `experience_level`) VALUES
	(23, 'Amos', 'amosbanza252@gmail.co', 'Amos@123', '0543883608', 'basic', 'beginner');

-- Dumping structure for view railway.v_admin_users
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_admin_users` (
	`user_id` INT UNSIGNED NOT NULL,
	`name` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`email` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`permissions_level` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`experience_level` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_0900_ai_ci'
);

-- Dumping structure for view railway.v_dashboard_stats
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_dashboard_stats` (
	`user_count` BIGINT NULL,
	`field_count` BIGINT NULL,
	`sensor_count` BIGINT NULL,
	`data_count` BIGINT NULL
);

-- Dumping structure for view railway.v_recent_data
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_recent_data` (
	`data_id` INT UNSIGNED NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	`value` DECIMAL(12,4) NOT NULL,
	`unit` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`field_id` INT UNSIGNED NOT NULL,
	`sensor_id` INT UNSIGNED NULL,
	`field_location` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_0900_ai_ci'
);

-- Dumping structure for table railway.vegetable
CREATE TABLE IF NOT EXISTS `vegetable` (
  `crop_id` int unsigned NOT NULL,
  `harvest_cycles` int NOT NULL,
  `irrigation_frequency` int NOT NULL,
  `nutrient_requirements` text,
  `sensitivity_to_pests` varchar(50) DEFAULT 'medium',
  PRIMARY KEY (`crop_id`),
  CONSTRAINT `vegetable_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`crop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.vegetable: ~2 rows (approximately)
INSERT INTO `vegetable` (`crop_id`, `harvest_cycles`, `irrigation_frequency`, `nutrient_requirements`, `sensitivity_to_pests`) VALUES
	(1, 3, 2, 'Nitrogen-rich soil', 'medium');
INSERT INTO `vegetable` (`crop_id`, `harvest_cycles`, `irrigation_frequency`, `nutrient_requirements`, `sensitivity_to_pests`) VALUES
	(4, 2, 3, 'Potassium-rich soil', 'high');

-- Dumping structure for table railway.weather_data
CREATE TABLE IF NOT EXISTS `weather_data` (
  `data_id` int unsigned NOT NULL,
  `temperature` decimal(5,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `rainfall` decimal(7,2) NOT NULL,
  `wind_speed` decimal(6,2) NOT NULL,
  PRIMARY KEY (`data_id`),
  CONSTRAINT `weather_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table railway.weather_data: ~3 rows (approximately)
INSERT INTO `weather_data` (`data_id`, `temperature`, `humidity`, `rainfall`, `wind_speed`) VALUES
	(2, 28.40, 65.00, 10.00, 15.00);
INSERT INTO `weather_data` (`data_id`, `temperature`, `humidity`, `rainfall`, `wind_speed`) VALUES
	(6, 55.00, 44.00, 33.00, 22.00);
INSERT INTO `weather_data` (`data_id`, `temperature`, `humidity`, `rainfall`, `wind_speed`) VALUES
	(7, 66.00, 55.00, 44.00, 33.00);

-- Dumping structure for trigger railway.before_crop_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_crop_insert` BEFORE INSERT ON `crop` FOR EACH ROW BEGIN
    IF NEW.yield_value IS NOT NULL AND NEW.yield_value < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Crop yield cannot be negative.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_crop_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_crop_update` BEFORE UPDATE ON `crop` FOR EACH ROW BEGIN
    IF NEW.yield_value IS NOT NULL AND NEW.yield_value < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Crop yield cannot be negative.';
    END IF;
    IF NEW.planting_date > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Planting date cannot be in the future.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_equipment_data_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_equipment_data_insert` BEFORE INSERT ON `equipment_data` FOR EACH ROW BEGIN
    IF NEW.usage_hours < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Equipment usage hours cannot be negative.';
    END IF;
    IF NEW.status NOT IN ('operational', 'maintenance', 'retired') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Equipment status must be operational, maintenance, or retired.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_equipment_data_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_equipment_data_update` BEFORE UPDATE ON `equipment_data` FOR EACH ROW BEGIN
    IF NEW.usage_hours < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Equipment usage hours cannot be negative.';
    END IF;
    IF NEW.status NOT IN ('operational', 'maintenance', 'retired') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Equipment status must be operational, maintenance, or retired.';
    END IF;
    -- Auto-set maintenance date to today if status changes TO 'maintenance'
    IF NEW.status = 'maintenance' AND OLD.status != 'maintenance' THEN
        SET NEW.maintenance_date = CURDATE();
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_field_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_field_insert` BEFORE INSERT ON `field` FOR EACH ROW BEGIN
    IF NEW.size <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Field size must be greater than zero.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_field_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_field_update` BEFORE UPDATE ON `field` FOR EACH ROW BEGIN
    IF NEW.size <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Field size must be greater than zero.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_irrigation_data_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_irrigation_data_insert` BEFORE INSERT ON `irrigation_data` FOR EACH ROW BEGIN
    IF NEW.water_amount <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Irrigation water amount must be greater than zero.';
    END IF;
    IF NEW.duration <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Irrigation duration must be greater than zero.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_sensor_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_sensor_insert` BEFORE INSERT ON `sensor` FOR EACH ROW BEGIN
    IF NEW.status NOT IN ('active', 'inactive', 'maintenance') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Sensor status must be active, inactive, or maintenance.';
    END IF;
    -- installation_date cannot be in the future
    IF NEW.installation_date > CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Sensor installation date cannot be in the future.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_sensor_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_sensor_update` BEFORE UPDATE ON `sensor` FOR EACH ROW BEGIN
    IF NEW.status NOT IN ('active', 'inactive', 'maintenance') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Sensor status must be active, inactive, or maintenance.';
    END IF;
    IF NEW.last_calibration_date IS NOT NULL AND NEW.last_calibration_date < NEW.installation_date THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Calibration date cannot be before installation date.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_soil_data_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_soil_data_insert` BEFORE INSERT ON `soil_data` FOR EACH ROW BEGIN
    IF NEW.ph_level < 0 OR NEW.ph_level > 14 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Soil pH must be between 0 and 14.';
    END IF;
    IF NEW.moisture < 0 OR NEW.moisture > 100 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Soil moisture must be between 0% and 100%.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_soil_data_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_soil_data_update` BEFORE UPDATE ON `soil_data` FOR EACH ROW BEGIN
    IF NEW.ph_level < 0 OR NEW.ph_level > 14 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Soil pH must be between 0 and 14.';
    END IF;
    IF NEW.moisture < 0 OR NEW.moisture > 100 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Soil moisture must be between 0% and 100%.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_user_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_user_insert` BEFORE INSERT ON `user` FOR EACH ROW BEGIN
    IF NEW.permissions_level NOT IN ('basic', 'standard', 'admin') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: permissions_level must be basic, standard, or admin.';
    END IF;
    IF NEW.experience_level NOT IN ('beginner', 'intermediate', 'expert') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: experience_level must be beginner, intermediate, or expert.';
    END IF;
    -- Normalise email to lowercase
    SET NEW.email = LOWER(NEW.email);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_user_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_user_update` BEFORE UPDATE ON `user` FOR EACH ROW BEGIN
    IF NEW.permissions_level NOT IN ('basic', 'standard', 'admin') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: permissions_level must be basic, standard, or admin.';
    END IF;
    IF NEW.experience_level NOT IN ('beginner', 'intermediate', 'expert') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: experience_level must be beginner, intermediate, or expert.';
    END IF;
    SET NEW.email = LOWER(NEW.email);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_weather_data_insert
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_weather_data_insert` BEFORE INSERT ON `weather_data` FOR EACH ROW BEGIN
    IF NEW.temperature < -60 OR NEW.temperature > 60 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Temperature must be between -60°C and 60°C.';
    END IF;
    IF NEW.humidity < 0 OR NEW.humidity > 100 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Humidity must be between 0% and 100%.';
    END IF;
    IF NEW.rainfall < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Rainfall cannot be negative.';
    END IF;
    IF NEW.wind_speed < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Wind speed cannot be negative.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger railway.before_weather_data_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `before_weather_data_update` BEFORE UPDATE ON `weather_data` FOR EACH ROW BEGIN
    IF NEW.temperature < -60 OR NEW.temperature > 60 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Temperature must be between -60°C and 60°C.';
    END IF;
    IF NEW.humidity < 0 OR NEW.humidity > 100 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Humidity must be between 0% and 100%.';
    END IF;
    IF NEW.rainfall < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Rainfall cannot be negative.';
    END IF;
    IF NEW.wind_speed < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Wind speed cannot be negative.';
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_admin_users`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_admin_users` AS select `user`.`user_id` AS `user_id`,`user`.`name` AS `name`,`user`.`email` AS `email`,`user`.`permissions_level` AS `permissions_level`,`user`.`experience_level` AS `experience_level` from `user`
;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_dashboard_stats`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_dashboard_stats` AS select (select count(0) from `user`) AS `user_count`,(select count(0) from `field`) AS `field_count`,(select count(0) from `sensor`) AS `sensor_count`,(select count(0) from `data_table`) AS `data_count`
;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_recent_data`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_recent_data` AS select `d`.`data_id` AS `data_id`,`d`.`timestamp` AS `timestamp`,`d`.`value` AS `value`,`d`.`unit` AS `unit`,`d`.`field_id` AS `field_id`,`d`.`sensor_id` AS `sensor_id`,`f`.`location` AS `field_location` from (`data_table` `d` join `field` `f` on((`d`.`field_id` = `f`.`field_id`)))
;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
