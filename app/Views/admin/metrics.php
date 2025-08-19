<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metrics | RioAnime Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Lexend -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- CountUp.js for animated counters -->
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.6.2/dist/countUp.umd.js"></script>
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/main.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/content.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/metrics.css') ?>">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3>RioAnime</h3>
                <div class="sidebar-brand-icon">
                    <i class="fas fa-video"></i>
                </div>
            </div>
            
            <div class="sidebar-divider"></div>
            
            <div class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="<?= site_url('admin') ?>" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= site_url('admin/metrics') ?>" class="nav-link active">
                            <i class="fas fa-chart-line"></i>
                            <span>Metrics</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= site_url('admin/accounts') ?>" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Accounts</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= site_url('admin/anime-manage') ?>" class="nav-link">
                            <i class="fas fa-film"></i>
                            <span>Anime Manage</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <a href="<?= site_url() ?>" target="_blank" class="sidebar-footer-link">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Site</span>
                </a>
                <a href="#" class="sidebar-footer-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container-fluid">
                    <button id="sidebar-toggle" class="navbar-toggler border-0" type="button">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if (!empty(session('user_profile'))): ?>
                                    <img src="<?= esc(session('user_profile')) ?>" alt="Admin" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff;">
                                <?php else: ?>
                                    <div class="rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; background: linear-gradient(45deg, #8B5CF6, #A855F7); color: white; font-size: 14px; border: 2px solid #fff;">
                                        <?= strtoupper(substr(session('username') ?? 'A', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span><?= esc(session('display_name') ?? session('username') ?? 'Admin') ?></span>
                                <!-- Debug: user_profile = "<?= esc(session('user_profile')) ?>" -->
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cogs me-2"></i> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Page Content -->
            <div class="content-wrapper">
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0">Metrics & Analytics</h1>
                        <div class="d-none d-sm-inline-block">
                            <button class="btn btn-primary btn-sm" onclick="refreshMetrics()">
                                <i class="fas fa-sync-alt"></i> Refresh Data
                            </button>
                        </div>
                    </div>
                    
                    <!-- Metrics Overview Row -->
                    <div class="row mb-4">
                        <!-- Total Views -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card metrics-card border-left-primary">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="metric-label">Total Views</div>
                                            <div class="metric-value text-primary" id="totalViews">
                                                <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
                                            </div>
                                            <div class="metric-change" id="viewsChange"></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Accounts -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card metrics-card border-left-success">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="metric-label">Total Accounts</div>
                                            <div class="metric-value text-success" id="totalAccounts">
                                                <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
                                            </div>
                                            <div class="metric-change" id="accountsChange"></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Currently Online -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card metrics-card border-left-info clickable-card" data-bs-toggle="modal" data-bs-target="#onlineUsersModal" style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="metric-label">
                                                <span class="online-indicator"></span>Currently Online
                                                <small class="text-muted d-block">Click to view details</small>
                                            </div>
                                            <div class="metric-value text-info" id="currentlyOnline">
                                                <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-wifi fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Views Today -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card metrics-card border-left-warning">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="metric-label">Views Today</div>
                                            <div class="metric-value text-warning" id="viewsToday">
                                                <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <!-- Views Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Views Analytics</h6>
                                    <div class="dropdown no-arrow">
                                        <select class="form-select form-select-sm" id="chartPeriod" onchange="updateChart()">
                                            <option value="hourly">Today (Hourly)</option>
                                            <option value="daily" selected>Last 30 Days</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="viewsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Device Analytics -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Device Analytics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="deviceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Data Tables Row -->
                    <div class="row">
                        <!-- Period Statistics -->
                        <div class="col-xl-6 col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Period Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-dark table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Period</th>
                                                    <th>Views</th>
                                                    <th>New Accounts</th>
                                                    <th>Online Users</th>
                                                </tr>
                                            </thead>
                                            <tbody id="periodStatsTable">
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <i class="fas fa-spinner fa-spin"></i> Loading...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Top Viewed Anime -->
                        <div class="col-xl-6 col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Viewed Anime</h6>
                                </div>
                                <div class="card-body">
                                    <div id="topAnimeList">
                                        <div class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin"></i> Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Online Users Modal -->
    <div class="modal fade" id="onlineUsersModal" tabindex="-1" aria-labelledby="onlineUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-light" id="onlineUsersModalLabel">
                        <i class="fas fa-wifi text-info me-2"></i>Currently Online Users
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="onlineUsersContent">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin text-info"></i>
                            <span class="text-light ms-2">Loading online users...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <div class="text-muted small me-auto">
                        <i class="fas fa-clock me-1"></i>Auto-refreshes every 30 seconds
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="<?= base_url('assets/js/admin/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin/metrics.js') ?>"></script>
    
    <script>
        // Set base URL for AJAX calls
        const baseUrl = '<?= base_url() ?>';
    </script>
</body>
</html>
