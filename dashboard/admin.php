<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole('dba');
?>
<div class="stats-grid" id="stats-container">
    <div class="stat-card cyan"><div class="skeleton" style="width: 100%; height: 80px; border-radius: 12px;"></div></div>
    <div class="stat-card violet"><div class="skeleton" style="width: 100%; height: 80px; border-radius: 12px;"></div></div>
    <div class="stat-card emerald"><div class="skeleton" style="width: 100%; height: 80px; border-radius: 12px;"></div></div>
    <div class="stat-card amber"><div class="skeleton" style="width: 100%; height: 80px; border-radius: 12px;"></div></div>
</div>
<div class="grid-2">
    <div class="card"><div class="card-header"><h3>Soil Trends</h3></div><div class="chart-container" id="soil-chart-wrapper"><div class="skeleton" style="width: 100%; height: 300px; border-radius: 8px;"></div><canvas id="soil-chart" style="display:none;"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Weather Trends</h3></div><div class="chart-container" id="weather-chart-wrapper"><div class="skeleton" style="width: 100%; height: 300px; border-radius: 8px;"></div><canvas id="weather-chart" style="display:none;"></canvas></div></div>
</div>
<div class="grid-2 mb-24" style="margin-top: 24px;">
    <div class="card"><div class="card-header"><h3>Data Records by Type</h3></div><div class="chart-container" style="max-height: 220px;" id="data-type-chart-wrapper"><div class="skeleton" style="width: 220px; height: 220px; border-radius: 50%; margin: 0 auto;"></div><canvas id="data-type-chart" style="display:none;"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Sensor Status Distribution</h3></div><div class="chart-container" style="max-height: 220px;" id="sensor-status-chart-wrapper"><div class="skeleton" style="width: 220px; height: 220px; border-radius: 50%; margin: 0 auto;"></div><canvas id="sensor-status-chart" style="display:none;"></canvas></div></div>
</div>
<div class="card mb-24">
    <div class="card-header"><h3>User Management</h3><a href="/pages/users.php" class="btn btn-sm btn-primary"><i class="fas fa-users-cog"></i> Manage All</a></div>
    <div class="table-wrapper">
        <table id="users-table">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Permissions</th></tr></thead>
            <tbody>
                <tr><td colspan="5"><div class="skeleton" style="width: 100%; height: 24px;"></div></td></tr>
                <tr><td colspan="5"><div class="skeleton" style="width: 100%; height: 24px;"></div></td></tr>
                <tr><td colspan="5"><div class="skeleton" style="width: 100%; height: 24px;"></div></td></tr>
                <tr><td colspan="5"><div class="skeleton" style="width: 100%; height: 24px;"></div></td></tr>
                <tr><td colspan="5"><div class="skeleton" style="width: 100%; height: 24px;"></div></td></tr>
            </tbody>
        </table>
    </div>
    <div id="users-pagination" style="padding: 1rem; display: flex; gap: 0.5rem; justify-content: center; align-items: center; border-top: 1px solid var(--border-color);">
    </div>
</div>

<style>
.skeleton {
    background: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.05) 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
}
@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<script>
let currentUsersPage = 1;

function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

function loadDashboardData(page = 1) {
    if(page !== 1) {
        document.querySelector('#users-table tbody').innerHTML = `
            <tr><td colspan="5"><div class="skeleton" style="width: 100%; height: 24px;"></div></td></tr>
            <tr><td colspan="5"><div class="skeleton" style="width: 100%; height: 24px;"></div></td></tr>
        `;
    }

    fetch(`/api/admin_dashboard_data.php?page=${page}`)
        .then(response => response.json())
        .then(data => {
            if (data.stats && page === 1) {
                document.getElementById('stats-container').innerHTML = `
                    <div class="stat-card cyan"><div class="stat-icon cyan"><i class="fas fa-users"></i></div><div class="stat-info"><span class="stat-value">${data.stats.userCount}</span><span class="stat-label">Users</span></div></div>
                    <div class="stat-card violet"><div class="stat-icon violet"><i class="fas fa-map-marked-alt"></i></div><div class="stat-info"><span class="stat-value">${data.stats.fieldCount}</span><span class="stat-label">Fields</span></div></div>
                    <div class="stat-card emerald"><div class="stat-icon emerald"><i class="fas fa-microchip"></i></div><div class="stat-info"><span class="stat-value">${data.stats.sensorCount}</span><span class="stat-label">Sensors</span></div></div>
                    <div class="stat-card amber"><div class="stat-icon amber"><i class="fas fa-database"></i></div><div class="stat-info"><span class="stat-value">${data.stats.dataCount}</span><span class="stat-label">Data Records</span></div></div>
                `;
            }

            if (data.users && data.users.data) {
                let tbody = '';
                data.users.data.forEach(u => {
                    const name = escapeHtml(u.name || '');
                    const email = escapeHtml(u.email || '');
                    const role = u.role.charAt(0).toUpperCase() + u.role.slice(1);
                    
                    tbody += `<tr>
                        <td>#${u.user_id}</td>
                        <td style="color:var(--text-primary);font-weight:500">${name}</td>
                        <td>${email}</td>
                        <td><span class="badge badge-info">${role}</span></td>
                        <td>${u.permissions_level}</td>
                    </tr>`;
                });
                document.querySelector('#users-table tbody').innerHTML = tbody;

                renderPagination(data.users.pagination);
            }

            if (data.charts && page === 1) {
                document.querySelectorAll('.chart-container .skeleton').forEach(el => el.remove());
                document.querySelectorAll('.chart-container canvas').forEach(el => el.style.display = 'block');

                if(typeof FarmCharts !== 'undefined') {
                    FarmCharts.renderSoilChart('soil-chart', null, data.charts.soilData);
                    FarmCharts.renderWeatherChart('weather-chart', null, data.charts.weatherData);
                    
                    FarmCharts.renderPieChart('data-type-chart', data.charts.dataType.labels, data.charts.dataType.values, {
                        'Soil': CHART_COLORS.amber,
                        'Weather': CHART_COLORS.blue,
                        'Irrigation': CHART_COLORS.cyan,
                        'Equipment': CHART_COLORS.violet
                    }, false);
                    
                    FarmCharts.renderPieChart('sensor-status-chart', data.charts.sensorStatus.labels, data.charts.sensorStatus.values, {
                        'Active': CHART_COLORS.emerald,
                        'Maintenance': CHART_COLORS.amber,
                        'Inactive': CHART_COLORS.red,
                        'Broken': CHART_COLORS.red
                    }, true);
                }
            }
        })
        .catch(err => console.error("Error loading dashboard data:", err));
}

function renderPagination(pagination) {
    const container = document.getElementById('users-pagination');
    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    if (pagination.current_page > 1) {
        html += `<button onclick="loadDashboardData(${pagination.current_page - 1})" class="btn btn-sm btn-secondary"><i class="fas fa-chevron-left"></i> Prev</button>`;
    }

    for (let i = 1; i <= pagination.total_pages; i++) {
        html += `<button onclick="loadDashboardData(${i})" class="btn btn-sm ${i === pagination.current_page ? 'btn-primary' : 'btn-secondary'}">${i}</button>`;
    }

    if (pagination.current_page < pagination.total_pages) {
        html += `<button onclick="loadDashboardData(${pagination.current_page + 1})" class="btn btn-sm btn-secondary">Next <i class="fas fa-chevron-right"></i></button>`;
    }

    container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
    loadDashboardData(1);
});
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
