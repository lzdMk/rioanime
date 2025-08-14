<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | RioAnime</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Lexend -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/main.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/content.css') ?>">
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
                        <a href="<?= site_url('admin') ?>" class="nav-link active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= site_url('admin/metrics') ?>" class="nav-link">
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
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> Admin
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
                        <h1 class="h3 mb-0">Dashboard</h1>
                        <div class="d-none d-sm-inline-block">
                            <span class="text-muted">Welcome to RioAnime Admin</span>
                        </div>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <!-- Send Notification Card -->
                        <div class="col-xl-8 col-lg-7 mx-auto">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-bell me-2"></i>Send Notification
                                    </h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Notification Actions:</div>
                                            <a class="dropdown-item" href="#" onclick="clearForm()">Clear Form</a>
                                            <a class="dropdown-item" href="#" onclick="previewNotification()">Preview</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="notificationForm" action="<?= site_url('admin/send-notification') ?>" method="POST">
                                        <!-- Target Selection -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <label for="target_type" class="form-label fw-bold">
                                                    <i class="fas fa-users me-2"></i>Send To
                                                </label>
                                                <select class="form-select" id="target_type" name="target_type" required>
                                                    <option value="">Select target...</option>
                                                    <option value="all">All Users</option>
                                                    <option value="specific">Specific User</option>
                                                    <option value="group">Group of Users</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Specific User Selection (Hidden by default) -->
                                        <div class="row mb-4" id="specific_user_section" style="display: none;">
                                            <div class="col-md-12">
                                                <label for="user_search" class="form-label fw-bold">
                                                    <i class="fas fa-user me-2"></i>Select User(s)
                                                </label>
                                                
                                                <!-- Search Input -->
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-search"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="user_search" placeholder="Search by username or email...">
                                                    <button class="btn btn-outline-secondary" type="button" id="load_users_btn" onclick="loadUsers()">
                                                        <i class="fas fa-refresh me-1"></i>Load
                                                    </button>
                                                </div>
                                                
                                                <!-- Users List Container -->
                                                <div id="users_container" class="border rounded p-3" style="max-height: 300px; overflow-y: auto; background: #f8f9fa;">
                                                    <div class="text-center text-muted py-3">
                                                        <i class="fas fa-users fa-2x mb-2"></i>
                                                        <div>Click "Load" to fetch users</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Selected Users Summary -->
                                                <div id="selected_users_summary" class="mt-2" style="display: none;">
                                                    <small class="text-muted">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        <span id="selected_count">0</span> user(s) selected
                                                    </small>
                                                </div>
                                                
                                                <!-- Hidden input to store selected user IDs -->
                                                <input type="hidden" id="selected_user_ids" name="user_ids" value="">
                                                
                                                <small class="form-text text-muted">
                                                    Search and select specific users to send the notification to. You can select multiple users.
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Group Selection (Hidden by default) -->
                                        <div class="row mb-4" id="group_section" style="display: none;">
                                            <div class="col-md-12">
                                                <label for="user_group" class="form-label fw-bold">
                                                    <i class="fas fa-users-cog me-2"></i>Select Group
                                                </label>
                                                <select class="form-select" id="user_group" name="user_group">
                                                    <option value="">Select group...</option>
                                                    <option value="premium">Premium Users</option>
                                                    <option value="active">Active Users (Last 30 days)</option>
                                                    <option value="new">New Users (Last 7 days)</option>
                                                    <option value="inactive">Inactive Users</option>
                                                </select>
                                                <small class="form-text text-muted">Choose a group to send notifications to multiple users.</small>
                                            </div>
                                        </div>

                                        <!-- Notification Title -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <label for="notification_title" class="form-label fw-bold">
                                                    <i class="fas fa-heading me-2"></i>Notification Title
                                                </label>
                                                <input type="text" class="form-control" id="notification_title" name="notification_title" placeholder="Enter notification title..." required maxlength="100">
                                                <div class="form-text">
                                                    <small class="text-muted">Maximum 100 characters. <span id="title_count">0</span>/100</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notification Message -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <label for="notification_message" class="form-label fw-bold">
                                                    <i class="fas fa-comment-alt me-2"></i>Message
                                                </label>
                                                <textarea class="form-control" id="notification_message" name="notification_message" rows="4" placeholder="Enter your notification message..." required maxlength="500"></textarea>
                                                <div class="form-text">
                                                    <small class="text-muted">Maximum 500 characters. <span id="message_count">0</span>/500</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notification Type -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="notification_type" class="form-label fw-bold">
                                                    <i class="fas fa-tag me-2"></i>Type
                                                </label>
                                                <select class="form-select" id="notification_type" name="notification_type" required>
                                                    <option value="">Select type...</option>
                                                    <option value="info">Information</option>
                                                    <option value="success">Success</option>
                                                    <option value="warning">Warning</option>
                                                    <option value="error">Error</option>
                                                    <option value="announcement">Announcement</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="notification_priority" class="form-label fw-bold">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>Priority
                                                </label>
                                                <select class="form-select" id="notification_priority" name="notification_priority" required>
                                                    <option value="">Select priority...</option>
                                                    <option value="low">Low</option>
                                                    <option value="normal" selected>Normal</option>
                                                    <option value="high">High</option>
                                                    <option value="urgent">Urgent</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Action URL (Optional) -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <label for="action_url" class="form-label fw-bold">
                                                    <i class="fas fa-link me-2"></i>Action URL (Optional)
                                                </label>
                                                <input type="url" class="form-control" id="action_url" name="action_url" placeholder="https://example.com/action">
                                                <small class="form-text text-muted">Optional: Add a link for users to take action on this notification.</small>
                                            </div>
                                        </div>

                                        <!-- Send Options -->
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <i class="fas fa-cogs me-2"></i>Send Options
                                                        </h6>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="send_immediately" name="send_immediately" checked>
                                                            <label class="form-check-label" for="send_immediately">
                                                                Send immediately
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="send_email" name="send_email">
                                                            <label class="form-check-label" for="send_email">
                                                                Also send via email (if user has email notifications enabled)
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Buttons -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="previewNotification()">
                                                        <i class="fas fa-eye me-2"></i>Preview
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning" onclick="clearForm()">
                                                        <i class="fas fa-eraser me-2"></i>Clear
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-paper-plane me-2"></i>Send Notification
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats Sidebar -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-chart-pie me-2"></i>Quick Stats
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <div class="bg-primary text-white rounded p-3">
                                                    <i class="fas fa-users fa-2x mb-2"></i>
                                                    <div class="h5 mb-0">1,234</div>
                                                    <small>Total Users</small>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="bg-success text-white rounded p-3">
                                                    <i class="fas fa-user-check fa-2x mb-2"></i>
                                                    <div class="h5 mb-0">856</div>
                                                    <small>Active Users</small>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="bg-info text-white rounded p-3">
                                                    <i class="fas fa-bell fa-2x mb-2"></i>
                                                    <div class="h5 mb-0">42</div>
                                                    <small>Sent Today</small>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <div class="bg-warning text-white rounded p-3">
                                                    <i class="fas fa-envelope-open fa-2x mb-2"></i>
                                                    <div class="h5 mb-0">89%</div>
                                                    <small>Read Rate</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Notifications -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-history me-2"></i>Recent Notifications
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle p-2 me-3">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">System Update</div>
                                                    <small class="text-muted">2 hours ago • All Users</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info rounded-circle p-2 me-3">
                                                    <i class="fas fa-info text-white"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">New Episodes</div>
                                                    <small class="text-muted">5 hours ago • Active Users</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded-circle p-2 me-3">
                                                    <i class="fas fa-exclamation text-white"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">Maintenance Notice</div>
                                                    <small class="text-muted">1 day ago • All Users</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="<?= base_url('assets/js/admin/main.js') ?>"></script>
    
    <!-- Dashboard Notification JS -->
    <script src="<?= base_url('assets/js/admin/dashboard.js') ?>"></script>
    
    <!-- Add base URL meta tag for AJAX calls -->
    <script>
        // Add base URL meta tag for JavaScript to use
        if (!document.querySelector('meta[name="base-url"]')) {
            const meta = document.createElement('meta');
            meta.name = 'base-url';
            meta.content = '<?= site_url() ?>';
            document.head.appendChild(meta);
        }
    </script>
</body>
</html>