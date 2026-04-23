-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: roundhouse.proxy.rlwy.net    Database: railway
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `agronomist`
--

DROP TABLE IF EXISTS `agronomist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agronomist` (
  `user_id` int unsigned NOT NULL,
  `specialization` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `agronomist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agronomist`
--

LOCK TABLES `agronomist` WRITE;
/*!40000 ALTER TABLE `agronomist` DISABLE KEYS */;
INSERT INTO `agronomist` VALUES (2,'Soil Analysis');
/*!40000 ALTER TABLE `agronomist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `analyzes`
--

DROP TABLE IF EXISTS `analyzes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analyzes` (
  `agronomist_id` int unsigned NOT NULL,
  `data_id` int unsigned NOT NULL,
  `analyzed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agronomist_id`,`data_id`),
  KEY `data_id` (`data_id`),
  CONSTRAINT `analyzes_ibfk_1` FOREIGN KEY (`agronomist_id`) REFERENCES `agronomist` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `analyzes_ibfk_2` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analyzes`
--

LOCK TABLES `analyzes` WRITE;
/*!40000 ALTER TABLE `analyzes` DISABLE KEYS */;
INSERT INTO `analyzes` VALUES (2,1,'2026-04-22 16:24:44'),(2,2,'2026-04-22 16:24:44'),(2,3,'2026-04-22 16:24:44'),(2,4,'2026-04-22 16:24:44'),(2,5,'2026-04-22 16:24:44');
/*!40000 ALTER TABLE `analyzes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crop`
--

DROP TABLE IF EXISTS `crop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crop` (
  `crop_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `planting_date` date NOT NULL DEFAULT (curdate()),
  `yield_value` decimal(10,2) DEFAULT NULL,
  `yield_unit` varchar(20) DEFAULT NULL,
  `field_id` int unsigned NOT NULL,
  PRIMARY KEY (`crop_id`),
  KEY `field_id` (`field_id`),
  CONSTRAINT `crop_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crop`
--

LOCK TABLES `crop` WRITE;
/*!40000 ALTER TABLE `crop` DISABLE KEYS */;
INSERT INTO `crop` VALUES (1,'Tomato','2026-04-01',500.00,'kg',1),(2,'Wheat','2026-03-15',2.50,'ton',2),(3,'Corn','2026-03-20',1.80,'ton',3),(4,'Potato','2026-04-05',700.00,'kg',4),(5,'Rice','2026-03-10',3.00,'ton',5);
/*!40000 ALTER TABLE `crop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_table`
--

DROP TABLE IF EXISTS `data_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_table` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_table`
--

LOCK TABLES `data_table` WRITE;
/*!40000 ALTER TABLE `data_table` DISABLE KEYS */;
INSERT INTO `data_table` VALUES (1,'2026-04-22 16:23:40',6.5000,'pH',1,1),(2,'2026-04-22 16:23:40',28.4000,'Celsius',2,2),(3,'2026-04-22 16:23:40',100.0000,'liters',3,3),(4,'2026-04-22 16:23:40',12.0000,'hours',4,4),(5,'2026-04-22 16:23:40',7.2000,'pH',5,5);
/*!40000 ALTER TABLE `data_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dba`
--

DROP TABLE IF EXISTS `dba`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dba` (
  `user_id` int unsigned NOT NULL,
  `role` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `dba_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dba`
--

LOCK TABLES `dba` WRITE;
/*!40000 ALTER TABLE `dba` DISABLE KEYS */;
INSERT INTO `dba` VALUES (4,'Database Administrator');
/*!40000 ALTER TABLE `dba` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipment_data`
--

DROP TABLE IF EXISTS `equipment_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipment_data` (
  `data_id` int unsigned NOT NULL,
  `type` varchar(100) NOT NULL,
  `usage_hours` decimal(8,2) NOT NULL,
  `maintenance_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'operational',
  PRIMARY KEY (`data_id`),
  CONSTRAINT `equipment_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipment_data`
--

LOCK TABLES `equipment_data` WRITE;
/*!40000 ALTER TABLE `equipment_data` DISABLE KEYS */;
INSERT INTO `equipment_data` VALUES (4,'Tractor',12.00,'2026-04-15','operational');
/*!40000 ALTER TABLE `equipment_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `farmer`
--

DROP TABLE IF EXISTS `farmer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `farmer` (
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `farmer_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `farmer`
--

LOCK TABLES `farmer` WRITE;
/*!40000 ALTER TABLE `farmer` DISABLE KEYS */;
INSERT INTO `farmer` VALUES (1),(5);
/*!40000 ALTER TABLE `farmer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `field`
--

DROP TABLE IF EXISTS `field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `field` (
  `field_id` int unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `size` decimal(10,2) NOT NULL,
  `irrigation_type` varchar(100) NOT NULL,
  `farmer_id` int unsigned NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY `farmer_id` (`farmer_id`),
  CONSTRAINT `field_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmer` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `field`
--

LOCK TABLES `field` WRITE;
/*!40000 ALTER TABLE `field` DISABLE KEYS */;
INSERT INTO `field` VALUES (1,'North Farm',12.50,'drip',1),(2,'East Farm',20.00,'sprinkler',5),(3,'South Farm',15.75,'flood',1),(4,'West Farm',30.00,'drip',5),(5,'Central Farm',18.25,'sprinkler',1);
/*!40000 ALTER TABLE `field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grain`
--

DROP TABLE IF EXISTS `grain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grain` (
  `crop_id` int unsigned NOT NULL,
  `growth_duration` int NOT NULL,
  `size` varchar(50) NOT NULL,
  `water_requirement` decimal(8,2) NOT NULL,
  `temperature_tolerance` varchar(50) NOT NULL,
  `harvest_method` varchar(100) NOT NULL,
  PRIMARY KEY (`crop_id`),
  CONSTRAINT `grain_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`crop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grain`
--

LOCK TABLES `grain` WRITE;
/*!40000 ALTER TABLE `grain` DISABLE KEYS */;
INSERT INTO `grain` VALUES (2,120,'large',5.50,'high','combine'),(3,90,'medium',4.00,'medium','mechanical'),(5,150,'small',6.00,'high','manual');
/*!40000 ALTER TABLE `grain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `growth_stage`
--

DROP TABLE IF EXISTS `growth_stage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `growth_stage` (
  `stage_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `crop_id` int unsigned NOT NULL,
  PRIMARY KEY (`stage_id`),
  UNIQUE KEY `name` (`name`,`crop_id`),
  KEY `crop_id` (`crop_id`),
  CONSTRAINT `growth_stage_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`crop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `growth_stage`
--

LOCK TABLES `growth_stage` WRITE;
/*!40000 ALTER TABLE `growth_stage` DISABLE KEYS */;
INSERT INTO `growth_stage` VALUES (1,'Seedling','Initial growth stage',1),(2,'Vegetative','Leaf development',2),(3,'Flowering','Flower stage',3),(4,'Maturity','Ready for harvest',4),(5,'Ripening','Final stage',5);
/*!40000 ALTER TABLE `growth_stage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `irrigation_data`
--

DROP TABLE IF EXISTS `irrigation_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `irrigation_data` (
  `data_id` int unsigned NOT NULL,
  `water_amount` decimal(10,2) NOT NULL,
  `irrigation_type` varchar(50) NOT NULL,
  `duration` int NOT NULL,
  PRIMARY KEY (`data_id`),
  CONSTRAINT `irrigation_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `irrigation_data`
--

LOCK TABLES `irrigation_data` WRITE;
/*!40000 ALTER TABLE `irrigation_data` DISABLE KEYS */;
INSERT INTO `irrigation_data` VALUES (3,100.00,'drip',30);
/*!40000 ALTER TABLE `irrigation_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensor`
--

DROP TABLE IF EXISTS `sensor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sensor` (
  `sensor_id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `installation_date` date NOT NULL DEFAULT (curdate()),
  `last_calibration_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `field_id` int unsigned NOT NULL,
  PRIMARY KEY (`sensor_id`),
  KEY `field_id` (`field_id`),
  CONSTRAINT `sensor_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensor`
--

LOCK TABLES `sensor` WRITE;
/*!40000 ALTER TABLE `sensor` DISABLE KEYS */;
INSERT INTO `sensor` VALUES (1,'soil','2026-01-10','2026-04-01','active',1),(2,'weather','2026-01-12','2026-04-02','active',2),(3,'irrigation','2026-01-15','2026-04-03','active',3),(4,'equipment','2026-01-18','2026-04-04','maintenance',4),(5,'soil','2026-01-20','2026-04-05','active',5);
/*!40000 ALTER TABLE `sensor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `soil_data`
--

DROP TABLE IF EXISTS `soil_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `soil_data` (
  `data_id` int unsigned NOT NULL,
  `ph_level` decimal(4,2) NOT NULL,
  `moisture` decimal(5,2) NOT NULL,
  `nutrient_levels` text,
  `sample_date` date DEFAULT (curdate()),
  PRIMARY KEY (`data_id`),
  CONSTRAINT `soil_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `soil_data`
--

LOCK TABLES `soil_data` WRITE;
/*!40000 ALTER TABLE `soil_data` DISABLE KEYS */;
INSERT INTO `soil_data` VALUES (1,6.50,45.00,'NPK Balanced','2026-04-20'),(5,7.20,50.00,'High Potassium','2026-04-20');
/*!40000 ALTER TABLE `soil_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technician`
--

DROP TABLE IF EXISTS `technician`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technician` (
  `user_id` int unsigned NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `field_id` int unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `field_id` (`field_id`),
  CONSTRAINT `technician_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `technician_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technician`
--

LOCK TABLES `technician` WRITE;
/*!40000 ALTER TABLE `technician` DISABLE KEYS */;
INSERT INTO `technician` VALUES (3,'Sensor Maintenance',1);
/*!40000 ALTER TABLE `technician` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'John Farmer','john@example.com','password123','5551001','basic','beginner'),(2,'Alice Agro','alice@example.com','password123','5551002','standard','expert'),(3,'David Tech','david@example.com','password123','5551003','standard','intermediate'),(4,'Sarah DBA','sarah@example.com','password123','5551004','admin','expert'),(5,'Michael Farmer','michael@example.com','password123','5551005','basic','intermediate');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vegetable`
--

DROP TABLE IF EXISTS `vegetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vegetable` (
  `crop_id` int unsigned NOT NULL,
  `harvest_cycles` int NOT NULL,
  `irrigation_frequency` int NOT NULL,
  `nutrient_requirements` text,
  `sensitivity_to_pests` varchar(50) DEFAULT 'medium',
  PRIMARY KEY (`crop_id`),
  CONSTRAINT `vegetable_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`crop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vegetable`
--

LOCK TABLES `vegetable` WRITE;
/*!40000 ALTER TABLE `vegetable` DISABLE KEYS */;
INSERT INTO `vegetable` VALUES (1,3,2,'Nitrogen-rich soil','medium'),(4,2,3,'Potassium-rich soil','high');
/*!40000 ALTER TABLE `vegetable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weather_data`
--

DROP TABLE IF EXISTS `weather_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weather_data` (
  `data_id` int unsigned NOT NULL,
  `temperature` decimal(5,2) NOT NULL,
  `humidity` decimal(5,2) NOT NULL,
  `rainfall` decimal(7,2) NOT NULL,
  `wind_speed` decimal(6,2) NOT NULL,
  PRIMARY KEY (`data_id`),
  CONSTRAINT `weather_data_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `data_table` (`data_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weather_data`
--

LOCK TABLES `weather_data` WRITE;
/*!40000 ALTER TABLE `weather_data` DISABLE KEYS */;
INSERT INTO `weather_data` VALUES (2,28.40,65.00,10.00,15.00);
/*!40000 ALTER TABLE `weather_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-23 10:04:15
