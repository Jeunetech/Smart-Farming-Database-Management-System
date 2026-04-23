/**
 * Smart Farming IoT — Core Application JS
 * Sidebar toggle, fetch helpers, toast notifications
 */

const App = {
    BASE: '',

    init() {
        this.initSidebar();
    },

    /* --- Sidebar --- */
    initSidebar() {
        const toggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        if (!toggle || !sidebar) return;
        toggle.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024 && sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) && e.target !== toggle) {
                sidebar.classList.remove('open');
            }
        });
    },

    /* --- Fetch Wrapper --- */
    async api(endpoint, options = {}) {
        const url = `${this.BASE}/api/${endpoint}`;
        const config = { headers: {}, ...options };
        if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
            config.headers['Content-Type'] = 'application/json';
            config.body = JSON.stringify(options.body);
        }
        try {
            const res = await fetch(url, config);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || `HTTP ${res.status}`);
            return data;
        } catch (err) {
            Toast.error(err.message || 'Request failed');
            throw err;
        }
    },

    /* --- Formatting --- */
    formatDate(dateStr) {
        if (!dateStr) return '—';
        return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    },
    formatDateTime(dateStr) {
        if (!dateStr) return '—';
        return new Date(dateStr).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    },
    statusBadge(status) {
        const cls = { active: 'badge-active', maintenance: 'badge-maintenance', inactive: 'badge-inactive', operational: 'badge-operational' };
        return `<span class="badge ${cls[status] || 'badge-info'}">${status}</span>`;
    },
    escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
};

/* --- Toast Notifications --- */
const Toast = {
    container: null,
    getContainer() {
        if (!this.container) this.container = document.getElementById('toast-container');
        return this.container;
    },
    show(message, type = 'info', duration = 3500) {
        const c = this.getContainer();
        if (!c) return;
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        const el = document.createElement('div');
        el.className = `toast toast-${type}`;
        el.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i><span>${message}</span>`;
        c.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateX(40px)'; setTimeout(() => el.remove(), 300); }, duration);
    },
    success(msg) { this.show(msg, 'success'); },
    error(msg) { this.show(msg, 'error'); },
    info(msg) { this.show(msg, 'info'); }
};

/* --- Modal --- */
const Modal = {
    open(title, bodyHtml, footerHtml = '') {
        document.getElementById('modal-title').textContent = title;
        document.getElementById('modal-body').innerHTML = bodyHtml;
        document.getElementById('modal-footer').innerHTML = footerHtml;
        document.getElementById('modal-overlay').classList.add('show');
    },
    close() {
        document.getElementById('modal-overlay').classList.remove('show');
    },
    init() {
        const overlay = document.getElementById('modal-overlay');
        const closeBtn = document.getElementById('modal-close');
        if (closeBtn) closeBtn.addEventListener('click', () => this.close());
        if (overlay) overlay.addEventListener('click', (e) => { if (e.target === overlay) this.close(); });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    App.init();
    Modal.init();
});
