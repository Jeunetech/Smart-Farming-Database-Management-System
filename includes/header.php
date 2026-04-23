<?php
/**
 * Common Header — Smart Farming IoT
 * Include this at the top of every page
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

$currentUser = getCurrentUser();
$currentRole = $currentUser['role'];
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Farming IoT — Intelligent agricultural management system with real-time sensor monitoring, crop tracking, and data analytics.">
    <title><?= $pageTitle ?? 'Smart Farming IoT' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-seedling"></i>
            </div>
            <span class="brand-text">SmartFarm</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <span class="nav-section-title">Main</span>
                <a href="<?= getDashboardUrl($currentRole) ?>" class="nav-link <?= $currentPage === $currentRole || $currentPage === 'admin' ? 'active' : '' ?>" id="nav-dashboard">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <?php if (in_array($currentRole, ['farmer', 'dba'])): ?>
            <div class="nav-section">
                <span class="nav-section-title">Farm Management</span>
                <a href="/pages/fields.php" class="nav-link <?= $currentPage === 'fields' ? 'active' : '' ?>" id="nav-fields">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Fields</span>
                </a>
                <a href="/pages/crops.php" class="nav-link <?= $currentPage === 'crops' ? 'active' : '' ?>" id="nav-crops">
                    <i class="fas fa-leaf"></i>
                    <span>Crops</span>
                </a>
            </div>
            <?php endif; ?>

            <?php if (in_array($currentRole, ['farmer', 'technician', 'dba'])): ?>
            <div class="nav-section">
                <span class="nav-section-title">IoT & Sensors</span>
                <a href="/pages/sensors.php" class="nav-link <?= $currentPage === 'sensors' ? 'active' : '' ?>" id="nav-sensors">
                    <i class="fas fa-microchip"></i>
                    <span>Sensors</span>
                </a>
                <a href="/pages/data.php" class="nav-link <?= $currentPage === 'data' ? 'active' : '' ?>" id="nav-data">
                    <i class="fas fa-database"></i>
                    <span>Sensor Data</span>
                </a>
            </div>
            <?php endif; ?>

            <?php if (in_array($currentRole, ['agronomist', 'dba'])): ?>
            <div class="nav-section">
                <span class="nav-section-title">Analysis</span>
                <a href="/pages/data.php" class="nav-link <?= $currentPage === 'data' ? 'active' : '' ?>" id="nav-analysis-data">
                    <i class="fas fa-chart-line"></i>
                    <span>Data Analysis</span>
                </a>
            </div>
            <?php endif; ?>

            <?php if ($currentRole === 'dba'): ?>
            <div class="nav-section">
                <span class="nav-section-title">Administration</span>
                <a href="/pages/users.php" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>" id="nav-users">
                    <i class="fas fa-users-cog"></i>
                    <span>Users</span>
                </a>
            </div>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($currentUser['name']) ?></span>
                    <span class="user-role"><?= ucfirst($currentRole) ?></span>
                </div>
            </div>
            <a href="/logout.php" class="nav-link logout-link" id="nav-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content" id="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">
                <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
            </div>
            <div class="topbar-actions">
                <span class="topbar-greeting">Welcome, <strong><?= htmlspecialchars($currentUser['name']) ?></strong></span>
            </div>
        </header>

        <!-- Page Content Container -->
        <div class="page-content">
    <?php endif; ?>
