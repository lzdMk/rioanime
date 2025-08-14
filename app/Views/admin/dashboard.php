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
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/dashboard.css') ?>">
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
                        <div class="col-12">
                            <div class="admin-card shadow mb-4">
                                <div class="admin-card-header py-3">
                                    <h6 class="m-0 font-weight-bold admin-text-primary">
                                        <i class="fas fa-bell me-2"></i>Send Notification
                                    </h6>
                                </div>
                                <div class="admin-card-body">
                                    <form id="notificationForm" action="<?= site_url('admin/send-notification') ?>" method="POST">
                                        <div class="row">
                                            <!-- Left Column - Form Fields -->
                                            <div class="col-lg-8">
                                                <!-- Target Selection -->
                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <label for="target_type" class="form-label admin-form-label fw-bold">
                                                            <i class="fas fa-users me-2"></i>Send To
                                                        </label>
                                                        <select class="form-select admin-form-control" id="target_type" name="target_type" required>
                                                            <option value="">Select target...</option>
                                                            <option value="all">All Users</option>
                                                            <option value="specific">Specific Users</option>
                                                            <option value="group">Group of Users</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Notification Title -->
                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <label for="notification_title" class="form-label admin-form-label fw-bold">
                                                            <i class="fas fa-heading me-2"></i>Notification Title
                                                        </label>
                                                        <input type="text" class="form-control admin-form-control" id="notification_title" name="notification_title" placeholder="Enter notification title..." required maxlength="100">
                                                        <div class="form-text">
                                                            <small class="admin-text-muted">Maximum 100 characters. <span id="title_count">0</span>/100</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Notification Message -->
                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <label for="notification_message" class="form-label admin-form-label fw-bold">
                                                            <i class="fas fa-comment-alt me-2"></i>Message
                                                        </label>
                                                        <textarea class="form-control admin-form-control" id="notification_message" name="notification_message" rows="4" placeholder="Enter your notification message..." required maxlength="500"></textarea>
                                                        <div class="form-text">
                                                            <small class="admin-text-muted">Maximum 500 characters. <span id="message_count">0</span>/500</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Notification Type & Priority -->
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <label for="notification_type" class="form-label admin-form-label fw-bold">
                                                            <i class="fas fa-tag me-2"></i>Type
                                                        </label>
                                                        <select class="form-select admin-form-control" id="notification_type" name="notification_type" required>
                                                            <option value="">Select type...</option>
                                                            <option value="info">Information</option>
                                                            <option value="success">Success</option>
                                                            <option value="warning">Warning</option>
                                                            <option value="error">Error</option>
                                                            <option value="announcement">Announcement</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="notification_priority" class="form-label admin-form-label fw-bold">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>Priority
                                                        </label>
                                                        <select class="form-select admin-form-control" id="notification_priority" name="notification_priority" required>
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
                                                        <label for="action_url" class="form-label admin-form-label fw-bold">
                                                            <i class="fas fa-link me-2"></i>Action URL (Optional)
                                                        </label>
                                                        <input type="url" class="form-control admin-form-control" id="action_url" name="action_url" placeholder="https://example.com/action">
                                                        <small class="form-text admin-text-muted">Optional: Add a link for users to take action on this notification.</small>
                                                    </div>
                                                </div>

                                                <!-- Send Options -->
                                                <div class="row mb-4">
                                                    <div class="col-md-12">
                                                        <div class="send-options-card">
                                                            <h6 class="card-title">
                                                                <i class="fas fa-cogs me-2"></i>Send Options
                                                            </h6>
                                                            
                                                            <!-- Send Immediately Option -->
                                                            <div class="schedule-checkbox mb-3">
                                                                <input class="form-check-input" type="checkbox" id="send_immediately" name="send_immediately" checked>
                                                                <label class="form-check-label" for="send_immediately">
                                                                    <i class="fas fa-bolt me-2"></i>Send immediately
                                                                </label>
                                                            </div>
                                                            
                                                            <!-- Schedule Option -->
                                                            <div class="schedule-checkbox mb-3">
                                                                <input class="form-check-input" type="checkbox" id="schedule_send" name="schedule_send">
                                                                <label class="form-check-label" for="schedule_send">
                                                                    <i class="fas fa-clock me-2"></i>Schedule for later
                                                                </label>
                                                            </div>
                                                            
                                                            <!-- Schedule Details (Hidden by default) -->
                                                            <div id="schedule_details" style="display: none;">
                                                                <div class="schedule-card">
                                                                    <h6 class="card-title">
                                                                        <i class="fas fa-calendar-alt me-2"></i>Schedule Details
                                                                    </h6>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label for="schedule_date" class="form-label admin-form-label fw-bold">
                                                                                <i class="fas fa-calendar me-2"></i>Date
                                                                            </label>
                                                                            <input type="date" class="form-control schedule-input" id="schedule_date" name="schedule_date" min="<?= date('Y-m-d') ?>">
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label for="schedule_time" class="form-label admin-form-label fw-bold">
                                                                                <i class="fas fa-clock me-2"></i>Time
                                                                            </label>
                                                                            <input type="time" class="form-control schedule-input" id="schedule_time" name="schedule_time">
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="alert alert-custom alert-custom-info d-flex align-items-center">
                                                                                <i class="fas fa-info-circle me-2"></i>
                                                                                <small>
                                                                                    Notifications will be sent at the specified date and time. 
                                                                                    Current server time: <strong><?= date('Y-m-d H:i:s') ?></strong>
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Submit Buttons -->
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                            <button type="button" class="btn admin-btn-outline" onclick="previewNotification()">
                                                                <i class="fas fa-eye me-2"></i>Preview
                                                            </button>
                                                            <button type="button" class="btn admin-btn-outline-warning" onclick="clearForm()">
                                                                <i class="fas fa-eraser me-2"></i>Clear
                                                            </button>
                                                            <button type="submit" class="btn admin-btn-primary">
                                                                <i class="fas fa-paper-plane me-2"></i>Send Notification
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Column - User Selection Panel -->
                                            <div class="col-lg-4">
                                                <!-- Specific User Selection Panel -->
                                                <div id="specific_user_section" style="display: none;">
                                                    <div class="admin-card h-100">
                                                        <div class="admin-card-header py-2">
                                                            <h6 class="m-0 admin-text-primary">
                                                                <i class="fas fa-users me-2"></i>Select Users
                                                            </h6>
                                                        </div>
                                                        <div class="admin-card-body p-0">
                                                            <!-- Search Box -->
                                                            <div class="p-3 border-bottom">
                                                                <div class="input-group">
                                                                    <span class="input-group-text">
                                                                        <i class="fas fa-search"></i>
                                                                    </span>
                                                                    <input type="text" class="form-control admin-form-control" id="user_search_input" placeholder="Search users..." autocomplete="off">
                                                                </div>
                                                                <small class="admin-text-muted">Search by username or display name</small>
                                                            </div>

                                                            <!-- User List Container -->
                                                            <div id="users_list_container" style="max-height: 400px; overflow-y: auto;">
                                                                <div class="text-center py-4 admin-text-muted">
                                                                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                                                                    <div>Loading users...</div>
                                                                </div>
                                                            </div>

                                                            <!-- Selected Users Footer -->
                                                            <div class="p-3 border-top">
                                                                <div id="selected_users_summary">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span class="admin-text-muted">
                                                                            <i class="fas fa-check-circle me-1"></i>
                                                                            <span id="selected_count">0</span> user(s) selected
                                                                        </span>
                                                                        <button type="button" class="btn btn-sm admin-btn-outline-warning" onclick="clearUserSelection()" style="display: none;" id="clear_selection_btn">
                                                                            <i class="fas fa-times me-1"></i>Clear All
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Group User Selection Panel -->
                                                <div id="group_user_section" style="display: none;">
                                                    <div class="admin-card h-100">
                                                        <div class="admin-card-header py-2">
                                                            <h6 class="m-0 admin-text-primary">
                                                                <i class="fas fa-users-cog me-2"></i>Select User Groups
                                                            </h6>
                                                        </div>
                                                        <div class="admin-card-body p-0">
                                                            <!-- Group List Container -->
                                                            <div id="groups_list_container" style="max-height: 400px; overflow-y: auto;">
                                                                <div class="p-3">
                                                                    <!-- Viewer Group -->
                                                                    <div class="user-card" data-group-type="viewer">
                                                                        <div class="d-flex align-items-center">
                                                                            <input class="form-check-input me-3" type="checkbox" id="group_viewer" value="viewer" onchange="toggleGroupSelection('viewer')">
                                                                            <div class="user-avatar" style="background: #6b7280 !important;">
                                                                                <i class="fas fa-eye"></i>
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <div class="fw-bold text-white mb-1">Viewer</div>
                                                                                <div class="small text-gray-300">Standard users with viewing access</div>
                                                                            </div>
                                                                            <div class="d-flex flex-column align-items-end">
                                                                                <span class="user-type-badge user-type-viewer mb-2">GROUP</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Moderator Group -->
                                                                    <div class="user-card" data-group-type="moderator">
                                                                        <div class="d-flex align-items-center">
                                                                            <input class="form-check-input me-3" type="checkbox" id="group_moderator" value="moderator" onchange="toggleGroupSelection('moderator')">
                                                                            <div class="user-avatar" style="background: #f59e0b !important;">
                                                                                <i class="fas fa-shield-alt"></i>
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <div class="fw-bold text-white mb-1">Moderator</div>
                                                                                <div class="small text-gray-300">Users with moderation privileges</div>
                                                                            </div>
                                                                            <div class="d-flex flex-column align-items-end">
                                                                                <span class="user-type-badge" style="background: #f59e0b !important; color: white !important;">GROUP</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Admin Group -->
                                                                    <div class="user-card" data-group-type="admin">
                                                                        <div class="d-flex align-items-center">
                                                                            <input class="form-check-input me-3" type="checkbox" id="group_admin" value="admin" onchange="toggleGroupSelection('admin')">
                                                                            <div class="user-avatar" style="background: #dc2626 !important;">
                                                                                <i class="fas fa-crown"></i>
                                                                            </div>
                                                                            <div class="flex-grow-1">
                                                                                <div class="fw-bold text-white mb-1">Admin</div>
                                                                                <div class="small text-gray-300">Administrative users with full access</div>
                                                                            </div>
                                                                            <div class="d-flex flex-column align-items-end">
                                                                                <span class="user-type-badge user-type-admin mb-2">GROUP</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Selected Groups Footer -->
                                                            <div class="p-3 border-top">
                                                                <div id="selected_groups_summary">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <span class="admin-text-muted">
                                                                            <i class="fas fa-check-circle me-1"></i>
                                                                            <span id="selected_groups_count">0</span> group(s) selected
                                                                        </span>
                                                                        <button type="button" class="btn btn-sm admin-btn-outline-warning" onclick="clearGroupSelection()" style="display: none;" id="clear_groups_btn">
                                                                            <i class="fas fa-times me-1"></i>Clear All
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- All Users Selection Panel -->
                                                <div id="all_users_section" style="display: none;">
                                                    <div class="admin-card h-100">
                                                        <div class="admin-card-header py-2">
                                                            <h6 class="m-0 admin-text-primary">
                                                                <i class="fas fa-globe me-2"></i>All Users Selected
                                                            </h6>
                                                        </div>
                                                        <div class="admin-card-body p-3">
                                                            <div class="text-center py-4">
                                                                <div class="mb-4">
                                                                    <i class="fas fa-users fa-4x admin-text-primary"></i>
                                                                </div>
                                                                <h5 class="admin-text-primary mb-3">Broadcasting to All Users</h5>
                                                                <p class="admin-text-muted mb-4">
                                                                    This notification will be sent to every registered user in the system.
                                                                </p>
                                                                <div class="alert alert-custom alert-custom-info mb-4">
                                                                    <i class="fas fa-info-circle me-2"></i>
                                                                    <small>This includes all user types: Viewers, Moderators, and Admins</small>
                                                                </div>
                                                                <div class="success-confirmation">
                                                                    <i class="fas fa-check-circle me-2"></i>
                                                                    <span class="fw-bold">All users will receive this notification</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden inputs to store selected IDs -->
                                        <input type="hidden" id="selected_user_ids" name="user_ids" value="">
                                        <input type="hidden" id="selected_group_ids" name="group_ids" value="">
                                    </form>
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