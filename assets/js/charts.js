/**
 * Smart Farming IoT — Chart.js Visualization Module
 */

const CHART_COLORS = {
    cyan: '#22d3ee', cyanBg: 'rgba(34,211,238,0.15)',
    violet: '#a78bfa', violetBg: 'rgba(167,139,250,0.15)',
    emerald: '#34d399', emeraldBg: 'rgba(52,211,153,0.15)',
    amber: '#fbbf24', amberBg: 'rgba(251,191,36,0.15)',
    red: '#f87171', redBg: 'rgba(248,113,113,0.15)',
    blue: '#60a5fa', blueBg: 'rgba(96,165,250,0.15)',
};

const CHART_DEFAULTS = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { labels: { color: '#94a3b8', font: { family: "'Inter', sans-serif", size: 12 }, boxWidth: 12, padding: 16 } },
        tooltip: {
            backgroundColor: 'rgba(17,24,39,0.95)', titleColor: '#f1f5f9', bodyColor: '#94a3b8',
            borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1, cornerRadius: 8, padding: 12,
            titleFont: { family: "'Inter', sans-serif", weight: '600' }, bodyFont: { family: "'Inter', sans-serif" }
        }
    },
    scales: {
        x: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.04)' } },
        y: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.04)' } }
    }
};

const FarmCharts = {
    instances: {},

    destroy(id) {
        if (this.instances[id]) { this.instances[id].destroy(); delete this.instances[id]; }
    },

    /** Soil Data — Dual axis line chart */
    async renderSoilChart(canvasId, fieldId = null, preloadedData = null) {
        this.destroy(canvasId);
        let data;
        if (preloadedData) {
            data = preloadedData;
        } else {
            let url = 'data.php?type=soil';
            if (fieldId) url += `&field_id=${fieldId}`;
            try { data = await App.api(url); } catch (e) { console.error(e); return; }
        }
        try {
            const records = Array.isArray(data) ? data : (data.data || []);
            if (!records.length) return;
            const labels = records.map(d => App.formatDate(d.sample_date || d.timestamp));
            const cfg = JSON.parse(JSON.stringify(CHART_DEFAULTS));
            cfg.scales = {
                x: CHART_DEFAULTS.scales.x,
                'y-ph': { position: 'left', min: 0, max: 14, title: { display: true, text: 'pH Level', color: '#64748b' }, ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                'y-moisture': { position: 'right', min: 0, max: 100, title: { display: true, text: 'Moisture %', color: '#64748b' }, ticks: { color: '#64748b' }, grid: { drawOnChartArea: false } }
            };
            this.instances[canvasId] = new Chart(document.getElementById(canvasId), {
                type: 'line', data: {
                    labels,
                    datasets: [
                        { label: 'pH Level', data: records.map(d => d.ph_level), yAxisID: 'y-ph', borderColor: CHART_COLORS.cyan, backgroundColor: CHART_COLORS.cyanBg, tension: 0.4, pointRadius: 4, pointBackgroundColor: CHART_COLORS.cyan, fill: true },
                        { label: 'Moisture %', data: records.map(d => d.moisture), yAxisID: 'y-moisture', borderColor: CHART_COLORS.violet, backgroundColor: CHART_COLORS.violetBg, tension: 0.4, pointRadius: 4, pointBackgroundColor: CHART_COLORS.violet, fill: true }
                    ]
                }, options: cfg
            });
        } catch (e) { console.error('Soil chart error:', e); }
    },

    /** Weather Data — Combined bar + line */
    async renderWeatherChart(canvasId, fieldId = null, preloadedData = null) {
        this.destroy(canvasId);
        let data;
        if (preloadedData) {
            data = preloadedData;
        } else {
            let url = 'data.php?type=weather';
            if (fieldId) url += `&field_id=${fieldId}`;
            try { data = await App.api(url); } catch (e) { console.error(e); return; }
        }
        try {
            const records = Array.isArray(data) ? data : (data.data || []);
            if (!records.length) return;
            const labels = records.map(d => App.formatDate(d.timestamp));
            const cfg = JSON.parse(JSON.stringify(CHART_DEFAULTS));
            cfg.scales = {
                x: CHART_DEFAULTS.scales.x,
                'y-temp': { position: 'left', title: { display: true, text: 'Temperature °C', color: '#64748b' }, ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                'y-rain': { position: 'right', title: { display: true, text: 'Rainfall mm', color: '#64748b' }, ticks: { color: '#64748b' }, grid: { drawOnChartArea: false } }
            };
            this.instances[canvasId] = new Chart(document.getElementById(canvasId), {
                type: 'bar', data: {
                    labels,
                    datasets: [
                        { label: 'Rainfall (mm)', data: records.map(d => d.rainfall), yAxisID: 'y-rain', backgroundColor: CHART_COLORS.blueBg, borderColor: CHART_COLORS.blue, borderWidth: 1, borderRadius: 4 },
                        { label: 'Temperature (°C)', data: records.map(d => d.temperature), yAxisID: 'y-temp', type: 'line', borderColor: CHART_COLORS.red, backgroundColor: 'transparent', tension: 0.4, pointRadius: 4, pointBackgroundColor: CHART_COLORS.red },
                        { label: 'Humidity (%)', data: records.map(d => d.humidity), yAxisID: 'y-temp', type: 'line', borderColor: CHART_COLORS.emerald, backgroundColor: 'transparent', tension: 0.4, pointRadius: 4, pointBackgroundColor: CHART_COLORS.emerald, borderDash: [5, 5] }
                    ]
                }, options: cfg
            });
        } catch (e) { console.error('Weather chart error:', e); }
    },

    /** Sensor Status — Doughnut chart */
    renderStatusDoughnut(canvasId, statusCounts) {
        this.destroy(canvasId);
        const labels = Object.keys(statusCounts);
        const values = Object.values(statusCounts);
        const colors = { active: CHART_COLORS.emerald, maintenance: CHART_COLORS.amber, inactive: CHART_COLORS.red };
        const bgColors = labels.map(l => colors[l] || CHART_COLORS.blue);
        this.instances[canvasId] = new Chart(document.getElementById(canvasId), {
            type: 'doughnut', data: { labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)), datasets: [{ data: values, backgroundColor: bgColors, borderColor: 'transparent', borderWidth: 0, spacing: 3 }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: CHART_DEFAULTS.plugins.legend, tooltip: CHART_DEFAULTS.plugins.tooltip } }
        });
    },

    /** Generic Pie/Doughnut Chart */
    renderPieChart(canvasId, labels, dataCounts, colors, isDoughnut = true) {
        this.destroy(canvasId);
        const bgColors = labels.map((l, i) => colors[l] || colors[i % colors.length] || CHART_COLORS.blue);
        this.instances[canvasId] = new Chart(document.getElementById(canvasId), {
            type: isDoughnut ? 'doughnut' : 'pie', 
            data: { 
                labels: labels.map(l => typeof l === 'string' ? l.charAt(0).toUpperCase() + l.slice(1) : l), 
                datasets: [{ data: dataCounts, backgroundColor: bgColors, borderColor: 'transparent', borderWidth: 0, spacing: 3 }] 
            },
            options: { 
                responsive: true, maintainAspectRatio: false, 
                cutout: isDoughnut ? '65%' : '0%', 
                plugins: { legend: CHART_DEFAULTS.plugins.legend, tooltip: CHART_DEFAULTS.plugins.tooltip } 
            }
        });
    },

    /** Generic Line Chart */
    renderLineChart(canvasId, labels, datasets) {
        this.destroy(canvasId);
        this.instances[canvasId] = new Chart(document.getElementById(canvasId), {
            type: 'line', data: { labels, datasets }, options: CHART_DEFAULTS
        });
    }
};
