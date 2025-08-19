<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts | RioAnime Admin</title>
    
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
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/pagination.css') ?>">
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
                        <a href="<?= site_url('admin/metrics') ?>" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Metrics</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= site_url('admin/accounts') ?>" class="nav-link active">
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
                        <h1 class="h3 mb-0">Accounts</h1>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-12">
                            <!-- Accounts Management -->
                            <div class="admin-card shadow mb-4">
                                <div class="admin-card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold admin-text-primary">User Accounts Management</h6>
                                    <button class="btn admin-btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                                        <i class="fas fa-plus me-1"></i> Add Account
                                    </button>
                                </div>
                                <div class="admin-card-body">
                                    <!-- Search and Filter -->
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control admin-form-control" id="accountSearch" placeholder="Search by username or email...">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-select admin-form-control" id="typeFilter">
                                                <option value="">All Types</option>
                                                <option value="viewer">Viewer</option>
                                                <option value="moderator">Moderator</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-select admin-form-control" id="perPageSelect">
                                                <option value="8" selected>8 per page</option>
                                                <option value="10">10 per page</option>
                                                <option value="15">15 per page</option>
                                                <option value="20">20 per page</option>
                                                <option value="custom">Custom...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control admin-form-control" id="customPerPageInput" placeholder="Min: 5" min="5" style="display: none;">
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn admin-btn-outline" id="refreshBtn">
                                                <i class="fas fa-sync-alt"></i> Refresh
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Accounts Table Container -->
                                    <div id="accountsTableContainer">
                                        <div class="text-center py-4">
                                            <div class="spinner-border admin-text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pagination Container -->
                                    <div id="accountsPagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title admin-text-primary"><i class="fas fa-plus me-2"></i>Add New Account</h5>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addAccountForm">
                    <div class="modal-body admin-modal-body">
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Username</label>
                            <input type="text" class="form-control admin-form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Display Name</label>
                            <input type="text" class="form-control admin-form-control" name="display_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Email</label>
                            <input type="email" class="form-control admin-form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Password</label>
                            <input type="password" class="form-control admin-form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Account Type</label>
                            <select class="form-select admin-form-control" name="type" required>
                                <option value="viewer">Viewer</option>
                                <option value="moderator">Moderator</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer admin-modal-footer">
                        <button type="button" class="btn admin-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn admin-btn-primary">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title admin-text-warning"><i class="fas fa-edit me-2"></i>Edit Account</h5>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAccountForm">
                    <input type="hidden" id="editAccountId" name="account_id">
                    <div class="modal-body admin-modal-body">
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Username</label>
                            <input type="text" class="form-control admin-form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Display Name</label>
                            <input type="text" class="form-control admin-form-control" id="editDisplayName" name="display_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Email</label>
                            <input type="email" class="form-control admin-form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Password <small class="admin-text-muted">(leave blank to keep current)</small></label>
                            <input type="password" class="form-control admin-form-control" id="editPassword" name="password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Account Type</label>
                            <select class="form-select admin-form-control" id="editType" name="type" required>
                                <option value="viewer">Viewer</option>
                                <option value="moderator">Moderator</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer admin-modal-footer">
                        <button type="button" class="btn admin-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn admin-btn-warning">Update Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Account Modal -->
    <div class="modal fade" id="viewAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title admin-text-info"><i class="fas fa-user me-2"></i>Account Details</h5>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body admin-modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div id="viewAvatar" class="mb-3"></div>
                            <h5 id="viewUsername" class="admin-text-primary"></h5>
                            <span id="viewType" class="badge"></span>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless admin-info-table">
                                <tr>
                                    <th class="admin-text-muted">Email:</th>
                                    <td id="viewEmail" class="admin-text-primary"></td>
                                </tr>
                                <tr>
                                    <th class="admin-text-muted">Display Name:</th>
                                    <td id="viewDisplayName" class="admin-text-primary"></td>
                                </tr>
                                <tr>
                                    <th class="admin-text-muted">Created:</th>
                                    <td id="viewCreated" class="admin-text-muted"></td>
                                </tr>
                                <tr>
                                    <th class="admin-text-muted">Watched Anime:</th>
                                    <td>
                                        <span id="viewWatchedCount" class="badge admin-badge-success"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="admin-text-muted">Followed Anime:</th>
                                    <td>
                                        <span id="viewFollowedCount" class="badge admin-badge-info"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Tabs for Watched and Followed -->
                    <ul class="nav nav-tabs admin-nav-tabs" id="accountTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active admin-tab-link" id="watched-tab" data-bs-toggle="tab" data-bs-target="#watched" type="button" role="tab">
                                <i class="fas fa-play me-1"></i>Watched Anime
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link admin-tab-link" id="followed-tab" data-bs-toggle="tab" data-bs-target="#followed" type="button" role="tab">
                                <i class="fas fa-heart me-1"></i>Followed Anime
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content admin-tab-content" id="accountTabContent">
                        <div class="tab-pane fade show active" id="watched" role="tabpanel">
                            <div class="anime-list-header mt-3 mb-2 d-flex justify-content-between align-items-center">
                                <span class="admin-text-muted small">Watched Anime List</span>
                                <button id="expandWatched" class="btn btn-sm admin-btn-outline-info">
                                    <i class="fas fa-expand-alt"></i> Show All
                                </button>
                            </div>
                            <div id="watchedAnimeList" class="admin-anime-list"></div>
                        </div>
                        <div class="tab-pane fade" id="followed" role="tabpanel">
                            <div class="anime-list-header mt-3 mb-2 d-flex justify-content-between align-items-center">
                                <span class="admin-text-muted small">Followed Anime List</span>
                                <button id="expandFollowed" class="btn btn-sm admin-btn-outline-info">
                                    <i class="fas fa-expand-alt"></i> Show All
                                </button>
                            </div>
                            <div id="followedAnimeList" class="admin-anime-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header py-2 border-danger">
                    <h6 class="modal-title text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h6>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body admin-modal-body p-3 text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                        <h6 class="admin-text-primary">Are you sure you want to delete this account?</h6>
                        <p class="admin-text-muted small mb-0">This action cannot be undone.</p>
                    </div>
                    <div id="deleteAccountUsername" class="admin-text-warning fw-bold"></div>
                </div>
                <div class="modal-footer admin-modal-footer py-2 justify-content-center">
                    <button type="button" class="btn admin-btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn admin-btn-danger btn-sm" id="confirmDeleteAccount">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
        <div id="liveToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script src="<?= base_url('assets/js/admin/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin/pagination.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin/accounts.js') ?>"></script>
</body>
</html>
