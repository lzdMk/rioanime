/**
 * Admin Metrics JavaScript
 * Handles metrics dashboard functionality with enhanced tracking
 */

class MetricsManager {
    constructor() {
        this.viewsChart = null;
        this.deviceChart = null;
        this.metricsData = {};
        this.refreshInterval = null;
        this.sessionTrackingInterval = null;
        this.countUpInstances = {};

        this.init();
    }

    init() {
        this.trackSession(); // Track current session
        this.loadMetricsData();
        this.loadDeviceAnalytics();
        this.startAutoRefresh();
        this.startSessionTracking();
        this.setupOnlineUsersModal(); // Setup online users modal

        // Bind event listeners
        document.getElementById('chartPeriod')?.addEventListener('change', () => this.updateChart());

        // Track page visibility for better online detection
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.trackSession();
                this.loadMetricsData();
            }
        });
    }

    startAutoRefresh() {
        // Auto-refresh every 30 seconds
        this.refreshInterval = setInterval(() => {
            this.loadMetricsData();
            this.updateOnlineCount();
        }, 30000);
    }

    startSessionTracking() {
        // Track session every 2 minutes to maintain online status
        this.sessionTrackingInterval = setInterval(() => {
            this.trackSession();
        }, 120000); // 2 minutes
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
        if (this.sessionTrackingInterval) {
            clearInterval(this.sessionTrackingInterval);
            this.sessionTrackingInterval = null;
        }
    }

    async trackSession() {
        try {
            await fetch(`${baseUrl}admin/trackSession`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
        } catch (error) {
            console.debug('Session tracking unavailable:', error);
        }
    }

    async loadMetricsData() {
        try {
            const response = await fetch(`${baseUrl}admin/getMetricsData`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.metricsData = data.data;
                this.updateMetricsDisplay();
                this.updateChart();
                this.updatePeriodTable();
                this.updateTopAnimeList();
            }
        } catch (error) {
            console.error('Error loading metrics:', error);
            this.showError('Failed to load metrics data');
        }
    }

    async loadDeviceAnalytics() {
        try {
            const response = await fetch(`${baseUrl}admin/getDeviceAnalytics`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateDeviceChart(data.data);
            }
        } catch (error) {
            console.error('Error loading device analytics:', error);
        }
    }

    updateMetricsDisplay() {
        // Update main metric cards with animation
        this.animateValue('totalViews', this.metricsData.views.total);
        this.animateValue('totalAccounts', this.metricsData.accounts.total);
        this.animateValue('currentlyOnline', this.metricsData.online.current);
        this.animateValue('viewsToday', this.metricsData.views.today);

        // Update change indicators
        const viewsChange = this.calculatePercentageChange(
            this.metricsData.views.today,
            this.metricsData.views.week / 7
        );
        const accountsChange = this.calculatePercentageChange(
            this.metricsData.accounts.today,
            this.metricsData.accounts.week / 7
        );

        this.updateChangeIndicator('viewsChange', viewsChange);
        this.updateChangeIndicator('accountsChange', accountsChange);
    }

    animateValue(elementId, endValue) {
        const element = document.getElementById(elementId);
        if (!element) return;

        // Destroy existing countUp instance
        if (this.countUpInstances[elementId]) {
            this.countUpInstances[elementId] = null;
        }

        const currentValue = parseInt(element.textContent) || 0;

        // Use CountUp.js for smooth animation
        if (typeof CountUp !== 'undefined') {
            this.countUpInstances[elementId] = new CountUp(elementId, endValue, {
                startVal: currentValue,
                duration: 1.5,
                separator: ',',
                decimal: '.',
                prefix: '',
                suffix: ''
            });

            this.countUpInstances[elementId].start();
        } else {
            // Fallback without animation
            element.textContent = this.formatNumber(endValue);
        }
    }

    updateChart() {
        const ctx = document.getElementById('viewsChart')?.getContext('2d');
        if (!ctx) return;

        const period = document.getElementById('chartPeriod')?.value || 'daily';

        if (this.viewsChart) {
            this.viewsChart.destroy();
        }

        let chartData, labels;

        if (period === 'hourly') {
            chartData = new Array(24).fill(0);
            labels = Array.from({ length: 24 }, (_, i) => `${i}:00`);

            if (this.metricsData.charts?.hourly) {
                this.metricsData.charts.hourly.forEach(item => {
                    chartData[item.hour] = item.views;
                });
            }
        } else {
            chartData = [];
            labels = [];

            if (this.metricsData.charts?.daily) {
                this.metricsData.charts.daily.forEach(item => {
                    labels.push(new Date(item.date).toLocaleDateString());
                    chartData.push(item.views);
                });
            }
        }

        this.viewsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Views',
                    data: chartData,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(75, 192, 192)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgb(75, 192, 192)',
                        borderWidth: 1
                    }
                }
            }
        });
    }

    updateDeviceChart(deviceData) {
        const ctx = document.getElementById('deviceChart')?.getContext('2d');
        if (!ctx) return;

        if (this.deviceChart) {
            this.deviceChart.destroy();
        }

        this.deviceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Mobile', 'Desktop', 'Tablet'],
                datasets: [{
                    data: [deviceData.mobile, deviceData.desktop, deviceData.tablet],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#2d3748',
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    updatePeriodTable() {
        const tableBody = document.getElementById('periodStatsTable');
        if (!tableBody || !this.metricsData.views) return;

        tableBody.innerHTML = `
            <tr>
                <td><strong>Today</strong></td>
                <td>${this.formatNumber(this.metricsData.views.today)}</td>
                <td>${this.formatNumber(this.metricsData.accounts.today)}</td>
                <td>${this.formatNumber(this.metricsData.online.today)}</td>
            </tr>
            <tr>
                <td><strong>This Week</strong></td>
                <td>${this.formatNumber(this.metricsData.views.week)}</td>
                <td>${this.formatNumber(this.metricsData.accounts.week)}</td>
                <td>${this.formatNumber(this.metricsData.online.week)}</td>
            </tr>
            <tr>
                <td><strong>This Month</strong></td>
                <td>${this.formatNumber(this.metricsData.views.month)}</td>
                <td>${this.formatNumber(this.metricsData.accounts.month)}</td>
                <td>${this.formatNumber(this.metricsData.online.month)}</td>
            </tr>
        `;
    }

    updateTopAnimeList() {
        const topAnimeContainer = document.getElementById('topAnimeList');
        if (!topAnimeContainer) return;

        if (!this.metricsData.topAnime || this.metricsData.topAnime.length === 0) {
            topAnimeContainer.innerHTML = '<p class="text-muted text-center">No data available</p>';
            return;
        }

        let html = '';
        this.metricsData.topAnime.forEach((anime, index) => {
            const rankClass = index < 3 ? 'text-warning' : 'text-primary';
            html += `
                <div class="top-anime-item">
                    <div class="anime-rank ${rankClass}">#${index + 1}</div>
                    <div class="flex-grow-1">
                        <div class="fw-bold">${this.escapeHtml(anime.title)}</div>
                        <small class="text-muted">ID: ${anime.anime_id}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">${this.formatNumber(anime.total_views)} views</span>
                    </div>
                </div>
            `;
        });

        topAnimeContainer.innerHTML = html;
    }

    async updateOnlineCount() {
        try {
            const response = await fetch(`${baseUrl}admin/getMetricsData`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.animateValue('currentlyOnline', data.data.online.current);
            }
        } catch (error) {
            console.error('Error updating online count:', error);
        }
    }

    refresh() {
        // Show loading indicators
        document.querySelectorAll('.metric-value').forEach(el => {
            el.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>';
        });

        this.trackSession(); // Track session on manual refresh
        this.loadMetricsData();
        this.loadDeviceAnalytics();
    }

    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    calculatePercentageChange(current, previous) {
        if (previous === 0) return current > 0 ? 100 : 0;
        return ((current - previous) / previous * 100).toFixed(1);
    }

    updateChangeIndicator(elementId, change) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const isPositive = change >= 0;
        element.className = `metric-change ${isPositive ? 'positive' : 'negative'}`;
        element.innerHTML = `<i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i> ${Math.abs(change)}%`;
    }

    updateElement(id, content) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = content;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showError(message) {
        // You can implement a toast notification system here
        console.error(message);
    }

    // Online Users Modal Functionality
    async loadOnlineUsers() {
        try {
            const response = await fetch(`${baseUrl}admin/getOnlineUsersList`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.renderOnlineUsers(data.data, data.total);
            } else {
                this.showOnlineUsersError('Failed to load online users');
            }
        } catch (error) {
            console.error('Error loading online users:', error);
            this.showOnlineUsersError('Failed to load online users');
        }
    }

    renderOnlineUsers(users, total) {
        const container = document.getElementById('onlineUsersContent');
        if (!container) return;

        if (!users || users.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-user-slash text-muted"></i>
                    <p class="text-light mt-2">No users currently online</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="online-users-header">
                <h6 class="text-light mb-0">
                    <i class="fas fa-users text-info me-2"></i>Online Users
                </h6>
                <span class="users-count">${total} user${total !== 1 ? 's' : ''} online</span>
            </div>
        `;

        users.forEach(user => {
            // Avatar logic: use uploaded avatar if available, otherwise use letter avatar
            let avatarHtml;
            if (user.user_profile && user.user_profile.trim() !== '') {
                // Show only image avatar with fallback
                avatarHtml = `<div class="avatar-container me-3" style="width: 40px; height: 40px;">
                                <img src="${user.user_profile}" alt="${this.escapeHtml(user.display_name || user.username)}" 
                                     class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color);"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                              </div>`;
            } else {
                // Show only letter avatar
                avatarHtml = `<div class="user-avatar-letter me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                   style="width: 40px; height: 40px; background: linear-gradient(45deg, #8B5CF6, #A855F7); color: white; font-size: 16px;">
                                ${this.escapeHtml((user.display_name || user.username).charAt(0).toUpperCase())}
                              </div>`;
            }

            const statusClass = user.user_type === 'admin' ? 'admin' : (user.user_type === 'user' ? 'user' : 'viewer');

            html += `
                <div class="user-card">
                    <div class="d-flex align-items-center">
                        ${avatarHtml}
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <span class="text-light fw-bold me-2">${this.escapeHtml(user.display_name || user.username)}</span>
                                <span class="user-status-badge ${statusClass}">${user.user_type}</span>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <span class="me-3">
                                    <i class="fas fa-at me-1"></i>${this.escapeHtml(user.username)}
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-desktop me-1"></i>
                                    <span class="device-badge">${user.device_type}</span>
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-globe me-1"></i>${user.ip_address}
                                </span>
                                <span>
                                    <i class="fas fa-clock me-1"></i>${user.time_ago}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    showOnlineUsersError(message) {
        const container = document.getElementById('onlineUsersContent');
        if (!container) return;

        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                <p class="text-light mt-2">${message}</p>
                <button class="btn btn-sm btn-outline-info" onclick="window.metricsManager.loadOnlineUsers()">
                    <i class="fas fa-redo me-1"></i>Retry
                </button>
            </div>
        `;
    }

    setupOnlineUsersModal() {
        const modal = document.getElementById('onlineUsersModal');
        if (!modal) return;

        // Load users when modal is shown
        modal.addEventListener('show.bs.modal', () => {
            this.loadOnlineUsers();
        });

        // Auto-refresh users while modal is open
        let modalRefreshInterval;
        modal.addEventListener('shown.bs.modal', () => {
            modalRefreshInterval = setInterval(() => {
                this.loadOnlineUsers();
            }, 30000); // Refresh every 30 seconds
        });

        modal.addEventListener('hidden.bs.modal', () => {
            if (modalRefreshInterval) {
                clearInterval(modalRefreshInterval);
                modalRefreshInterval = null;
            }
        });
    }

    // Cleanup when page unloads
    destroy() {
        this.stopAutoRefresh();
        if (this.viewsChart) this.viewsChart.destroy();
        if (this.deviceChart) this.deviceChart.destroy();
    }
}

// Initialize metrics manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    window.metricsManager = new MetricsManager();

    // Cleanup on page unload
    window.addEventListener('beforeunload', function () {
        if (window.metricsManager) {
            window.metricsManager.destroy();
        }
    });
});

// Global functions for backward compatibility
function refreshMetrics() {
    if (window.metricsManager) {
        window.metricsManager.refresh();
    }
}

function updateChart() {
    if (window.metricsManager) {
        window.metricsManager.updateChart();
    }
}
