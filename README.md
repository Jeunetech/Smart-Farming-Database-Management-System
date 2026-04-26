# Smart Farming Database Management System

![Language](https://img.shields.io/badge/Language-PHP%20%7C%20HTML%20%7C%20CSS%20%7C%20JS-blue)
![Database](https://img.shields.io/badge/Database-MySQL%20(Railway)-orange)

## 📌 Project Overview
The **Smart Farming Database Management System** is a full-stack IoT web application designed to manage, monitor, and optimize modern agricultural processes. The platform features a modular architecture with a secure, RESTful PHP backend and a dynamic, role-based frontend.

The system embraces a modern **Dark-Mode Glassmorphism** design pattern and provides role-specific dashboards for **Farmers, Agronomists, Technicians, and Admins**. It uses **Chart.js** for data visualization, allowing users to track real-time IoT sensor data, monitor crop health, and manage agricultural resources efficiently.

## 🚀 Features & Capabilities
* **Role-Based Access Control:** Distinct interfaces and privileges for Farmers, Agronomists, Technicians, and Administrators.
* **IoT Data Visualization:** Integration with Chart.js to visualize metrics like soil moisture, temperature, and resource usage.
* **CRUD RESTful API:** A modular PHP backend that securely manages the database schema and endpoints.
* **Remote Database Integration:** Successfully connected to a cloud-hosted MySQL database via **Railway**, ensuring continuous data availability across environments.
* **Modern UI/UX:** Responsive, dark-themed glassmorphism aesthetic for an intuitive user experience.

## 🛠️ Technologies Used
* **Frontend:** HTML5, Vanilla CSS (Dark Mode/Glassmorphism), JavaScript (Vanilla), Chart.js
* **Backend:** PHP (PDO, RESTful API architecture)
* **Database:** MySQL 8.0 (Hosted remotely on Railway)
* **Hosting / Cloud:** Railway (Database Infrastructure), standard Web Hosting for the application.

## 🌐 Railway Database Connection
The application is configured to communicate with a remote **Railway MySQL** database. We established this connection by overriding the default local configurations (`mysql.railway.internal`) with the **Railway Public TCP Proxy URL**. This allows the PHP application to run on any normal web hosting environment or local XAMPP server while seamlessly fetching data from the cloud database. Environment variables are used to securely manage connection parameters like `MYSQLHOST` and `MYSQLPORT`.

## 👥 Team Members & Contributions

This project was developed collaboratively as a group assignment. Below are the specific roles and responsibilities of the team:

### 👤 Mahisa Ahadi (22213656) – Database Designer (ERD & Schema)
* **Responsibilities:** 
  * Design the full database structure (minimum 6 tables)
  * Create ER Diagram (ERD)
  * Define: Primary Keys, Foreign Keys, Relationships (1-1, 1-N, N-M)
  * Convert ERD into Relational Model
  * Decide constraints (NOT NULL, UNIQUE, DEFAULT, etc.)
* **Deliverables:** ER Diagram (image or PDF) and Relational schema

### 👤 Audry Kyungu Mwansa (22109381) – Database Implementation (DDL & DML)
* **Responsibilities:** 
  * Write all DDL (Data Definition Language): `CREATE TABLE`, Constraints
  * Write DML (Data Manipulation Language): `INSERT`, `UPDATE`, `DELETE`
  * Populate database with sample data
* **Deliverables:** SQL scripts for creating tables and inserting sample data

### 👤 Taraneh Khorsihidi (22214139) – Advanced SQL & PL/SQL Developer
* **Responsibilities:** 
  * Write 5–7 complex SQL queries, including: `JOIN`, `GROUP BY`, `ORDER BY`, Aggregate functions (`SUM`, `AVG`, `COUNT`)
  * Write at least 5 PL/SQL blocks: Procedures, Functions, Triggers
* **Deliverables:** SQL queries with outputs and PL/SQL code with explanations

### 👤 Maryam Ahmadi (22314265) – Frontend / GUI Developer
* **Responsibilities:** 
  * Design and implement GUI (User Interface) using Web technologies (HTML/CSS/JavaScript)
  * Create at least 5 screens, such as: Login/Register page, Dashboard, Data management (Insert/Update/Delete)
  * Connect UI to database (basic CRUD operations)
* **Deliverables:** UI code and Screenshots of working app

### 👤 Rana Razavi (22101014) – Backend & Deployment + GitHub Manager
* **Responsibilities:** 
  * Handle database connection (API or direct connection)
  * Manage deployment using platforms like Railway
  * Setup project on GitHub (Create repository, add collaborators, upload all files including ERD, SQL, code)
  * Ensure everything works together
* **Deliverables:** Working deployed database/app and GitHub repository (public link)
