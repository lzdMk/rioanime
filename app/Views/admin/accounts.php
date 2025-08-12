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
                                        <div class="col-md-6">
                                            <input type="text" class="form-control admin-form-control" id="searchInput" placeholder="Search by username or email...">
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select admin-form-control" id="typeFilter">
                                                <option value="">All Types</option>
                                                <option value="user">User</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn admin-btn-outline" id="refreshBtn">
                                                <i class="fas fa-sync-alt"></i> Refresh
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Accounts Table -->
                                    <div class="table-responsive">
                                        <table class="table admin-table table-hover" id="accountsTable">
                                            <thead class="admin-table-header">
                                                <tr>
                                                    <th>Avatar</th>
                                                    <th>Username</th>
                                                    <th>Email</th>
                                                    <th>Type</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="admin-table-body">
                                                <?php if (!empty($accounts)): ?>
                                                    <?php foreach ($accounts as $account): ?>
                                                        <tr data-account-id="<?= $account['id'] ?>">
                                                            <td class="text-center">
                                                                <?php if (!empty($account['user_profile'])): ?>
                                                                    <img src="<?= esc($account['user_profile']) ?>" alt="Avatar" class="admin-avatar">
                                                                <?php else: ?>
                                                                    <div class="admin-avatar-letter">
                                                                        <?= strtoupper(substr($account['username'], 0, 1)) ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="admin-text-primary fw-bold"><?= esc($account['username']) ?></td>
                                                            <td class="admin-text-muted"><?= esc($account['email']) ?></td>
                                                            <td>
                                                                <span class="badge admin-badge-<?= $account['type'] === 'admin' ? 'danger' : 'primary' ?>">
                                                                    <?= ucfirst(esc($account['type'])) ?>
                                                                </span>
                                                            </td>
                                                            <td class="admin-text-muted"><?= date('M j, Y', strtotime($account['created_at'])) ?></td>
                                                            <td>
                                                                <div class="btn-group admin-action-buttons" role="group">
                                                                    <button class="btn admin-btn-info btn-sm view-account" data-id="<?= $account['id'] ?>" title="View Details">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    <button class="btn admin-btn-warning btn-sm edit-account" data-id="<?= $account['id'] ?>" title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button class="btn admin-btn-danger btn-sm delete-account" data-id="<?= $account['id'] ?>" title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center admin-text-muted py-4">
                                                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                                                            <p class="mb-0">No accounts found</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if (isset($pager)): ?>
                                        <div class="d-flex justify-content-center">
                                            <?= $pager->links() ?>
                                        </div>
                                    <?php endif; ?>
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
                                <option value="user">User</option>
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
                                <option value="user">User</option>
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
    <script src="<?= base_url('assets/js/admin/accounts.js') ?>"></script>
</body>
</html>
