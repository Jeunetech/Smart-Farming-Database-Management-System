# Smart Farming Database Management System

![Project Status](https://img.shields.io/badge/Status-Active-success)
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

This project was developed collaboratively as a group assignment. Below are the specific contributions of each team member:

| Team Member | Student ID | Contributions |
| :--- | :---: | :--- |
| **Jeunetech AUDRY MWANSA** | *(Not provided)* | Configured the cloud infrastructure and successfully set up the MySQL Database on **Railway**. |
| **Taraneh Khorshidi** | 22214139 | Developed both the **Front-End** and **Back-End** architecture, and successfully integrated the PHP application to connect with the Railway database. |
| **Mahisa Ahadi** | 22213656 | Designed and extracted the **Database Schemas**, ensuring relational integrity and defining the project's core data structure. |


