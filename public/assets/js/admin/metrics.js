/**
 * Admin Metrics JavaScript
 * Handles metrics dashboard functionality
 */

class MetricsManager {
    constructor() {
        this.viewsChart = null;
        this.deviceChart = null;
        this.metricsData = {};
        this.refreshInterval = null;
        
        this.init();
    }
    
    init() {
        this.loadMetricsData();
        this.loadDeviceAnalytics();
        this.startAutoRefresh();
        
        // Bind event listeners
        document.getElementById('chartPeriod')?.addEventListener('change', () => this.updateChart());
    }
    
    startAutoRefresh() {
        // Auto-refresh every 30 seconds
        this.refreshInterval = setInterval(() => {
            this.loadMetricsData();
            this.updateOnlineCount();
        }, 30000);
    }
    
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
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
        // Update main metric cards
        this.updateElement('totalViews', this.formatNumber(this.metricsData.views.total));
        this.updateElement('totalAccounts', this.formatNumber(this.metricsData.accounts.total));
        this.updateElement('currentlyOnline', this.formatNumber(this.metricsData.online.current));
        this.updateElement('viewsToday', this.formatNumber(this.metricsData.views.today));
        
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
            labels = Array.from({length: 24}, (_, i) => `${i}:00`);
            
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
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
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
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
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
                this.updateElement('currentlyOnline', this.formatNumber(data.data.online.current));
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
}

// Initialize metrics manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.metricsManager = new MetricsManager();
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
