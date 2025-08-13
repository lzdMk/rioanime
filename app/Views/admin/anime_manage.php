<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Manage | RioAnime Admin</title>
    
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
    <link rel="stylesheet" href="<?= base_url('assets/css/admin/anime-modal.css') ?>">
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
                        <a href="<?= site_url('admin/accounts') ?>" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Accounts</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= site_url('admin/anime-manage') ?>" class="nav-link active">
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
                        <h1 class="h3 mb-0">Anime Manage</h1>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-12">
                            <!-- Anime Management -->
                            <div class="admin-card shadow mb-4">
                                <div class="admin-card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold admin-text-primary">Anime Library Management</h6>
                                    <button class="btn admin-btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAnimeModal">
                                        <i class="fas fa-plus me-1"></i> Add Anime
                                    </button>
                                </div>
                                <div class="admin-card-body">
                                    <!-- Search and Filter -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control admin-form-control" id="animeSearch" placeholder="Search by title or genre...">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-select admin-form-control" id="typeFilter">
                                                <option value="">All Types</option>
                                                <option value="TV">TV Series</option>
                                                <option value="Movie">Movie</option>
                                                <option value="OVA">OVA</option>
                                                <option value="Special">Special</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-select admin-form-control" id="statusFilter">
                                                <option value="">All Status</option>
                                                <option value="Finished Airing">Finished Airing</option>
                                                <option value="Airing">Airing</option>
                                                <option value="Incomplete">Incomplete</option>
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
                                        <div class="col-md-1">
                                            <input type="number" class="form-control admin-form-control" id="customPerPageInput" placeholder="Min: 5" min="5" style="display: none;">
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn admin-btn-outline w-100" id="refreshBtn">
                                                <i class="fas fa-sync-alt"></i> Refresh
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Anime Table Container -->
                                    <div id="animeTableContainer">
                                        <div class="text-center py-4">
                                            <div class="spinner-border admin-text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pagination Container -->
                                    <div id="animePagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Anime Modal -->
    <div class="modal fade" id="addAnimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header py-2">
                    <h6 class="modal-title admin-text-primary mb-0"><i class="fas fa-plus me-2"></i>Add New Anime</h6>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addAnimeForm">
                    <div class="modal-body admin-modal-body p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Title *</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" name="title" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Type *</label>
                                <select class="form-select admin-form-control form-control-sm" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="TV">TV Series</option>
                                    <option value="Movie">Movie</option>
                                    <option value="OVA">OVA</option>
                                    <option value="Special">Special</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label admin-form-label small">Language *</label>
                                <select class="form-select admin-form-control form-control-sm" name="language" required>
                                    <option value="">Select Language</option>
                                    <option value="Sub">Sub</option>
                                    <option value="Dub">Dub</option>
                                    <option value="Japanese">Japanese</option>
                                    <option value="English">English</option>
                                    <option value="Chinese">Chinese</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label admin-form-label small">Total Episodes</label>
                                <input type="number" class="form-control admin-form-control form-control-sm" name="total_ep" min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label admin-form-label small">Rating</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" name="ratings" placeholder="e.g., 8.5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Status *</label>
                                <select class="form-select admin-form-control form-control-sm" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Finished Airing">Finished Airing</option>
                                    <option value="airing">Airing</option>
                                    <option value="incomplete">Incomplete</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Studios</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" name="studios" placeholder="Studio name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">Genres</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" name="genres" placeholder="Action, Adventure, Comedy">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">Background Image URL</label>
                                <input type="url" class="form-control admin-form-control form-control-sm" name="backgroundImage">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">URLs (streaming links)</label>
                                <textarea class="form-control admin-form-control form-control-sm" name="urls" rows="2" placeholder="One URL per line"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">Synopsis</label>
                                <textarea class="form-control admin-form-control form-control-sm" name="synopsis" rows="3" placeholder="Brief description of the anime"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer admin-modal-footer py-2">
                        <button type="button" class="btn admin-btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn admin-btn-primary btn-sm">Add Anime</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Anime Modal -->
    <div class="modal fade" id="editAnimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header py-2">
                    <h6 class="modal-title admin-text-warning mb-0"><i class="fas fa-edit me-2"></i>Edit Anime</h6>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAnimeForm">
                    <input type="hidden" id="editAnimeId" name="anime_id">
                    <div class="modal-body admin-modal-body p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Title *</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" id="editTitle" name="title" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Type *</label>
                                <select class="form-select admin-form-control form-control-sm" id="editType" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="TV">TV Series</option>
                                    <option value="Movie">Movie</option>
                                    <option value="OVA">OVA</option>
                                    <option value="Special">Special</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label admin-form-label small">Language *</label>
                                <select class="form-select admin-form-control form-control-sm" id="editLanguage" name="language" required>
                                    <option value="">Select Language</option>
                                    <option value="sub">Sub</option>
                                    <option value="dub">Dub</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label admin-form-label small">Total Episodes</label>
                                <input type="number" class="form-control admin-form-control form-control-sm" id="editTotalEp" name="total_ep" min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label admin-form-label small">Rating</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" id="editRatings" name="ratings" placeholder="e.g., 8.5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Status *</label>
                                <select class="form-select admin-form-control form-control-sm" id="editStatus" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Finished Airing">Finished Airing</option>
                                    <option value="Airing">Airing</option>
                                    <option value="Incomplete">Incomplete</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label admin-form-label small">Studios</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" id="editStudios" name="studios" placeholder="Studio name">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">Genres</label>
                                <input type="text" class="form-control admin-form-control form-control-sm" id="editGenres" name="genres" placeholder="Action, Adventure, Comedy">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">Background Image URL</label>
                                <input type="url" class="form-control admin-form-control form-control-sm" id="editBackgroundImage" name="backgroundImage">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">URLs (streaming links)</label>
                                <textarea class="form-control admin-form-control form-control-sm" id="editUrls" name="urls" rows="2" placeholder="One URL per line"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label admin-form-label small">Synopsis</label>
                                <textarea class="form-control admin-form-control form-control-sm" id="editSynopsis" name="synopsis" rows="3" placeholder="Brief description of the anime"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer admin-modal-footer py-2">
                        <button type="button" class="btn admin-btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn admin-btn-warning btn-sm">Update Anime</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Anime Modal -->
    <div class="modal fade" id="viewAnimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header py-2">
                    <h6 class="modal-title admin-text-info mb-0"><i class="fas fa-film me-2"></i>Anime Details</h6>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body admin-modal-body p-3">
                    <div class="row g-3">
                        <!-- Left Column: Image and Basic Info -->
                        <div class="col-md-4">
                            <div id="viewAnimeImage" class="mb-2"></div>
                            <h6 id="viewAnimeTitle" class="admin-text-primary mb-1 fw-bold"></h6>
                            <div class="mb-2">
                                <span id="viewAnimeType" class="badge me-1 small"></span>
                                <span id="viewAnimeStatus" class="badge small"></span>
                            </div>
                            <div id="viewAnimeRating" class="admin-text-warning small fw-bold"></div>
                        </div>
                        
                        <!-- Right Column: Details -->
                        <div class="col-md-8">
                            <div class="anime-details-grid">
                                <div class="anime-detail-item">
                                    <span class="detail-label">Language:</span>
                                    <span id="viewAnimeLanguage" class="detail-value"></span>
                                </div>
                                <div class="anime-detail-item">
                                    <span class="detail-label">Episodes:</span>
                                    <span id="viewAnimeEpisodes" class="detail-value"></span>
                                </div>
                                <div class="anime-detail-item">
                                    <span class="detail-label">Genres:</span>
                                    <span id="viewAnimeGenres" class="detail-value"></span>
                                </div>
                                <div class="anime-detail-item">
                                    <span class="detail-label">Studios:</span>
                                    <span id="viewAnimeStudios" class="detail-value"></span>
                                </div>
                            </div>
                            
                            <!-- Synopsis Section -->
                            <div class="mt-3">
                                <h6 class="section-title">Synopsis:</h6>
                                <div id="viewAnimeSynopsis" class="content-box synopsis-box"></div>
                            </div>
                            
                            <!-- Streaming URLs Section -->
                            <div class="mt-3">
                                <h6 class="section-title">Streaming URLs:</h6>
                                <div id="viewAnimeUrls" class="content-box urls-box"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteAnimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header py-2 border-danger">
                    <h6 class="modal-title text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h6>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body admin-modal-body p-3 text-center">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                        <h6 class="admin-text-primary">Are you sure you want to delete this anime?</h6>
                        <p class="admin-text-muted small mb-0">This action cannot be undone.</p>
                    </div>
                    <div id="deleteAnimeTitle" class="admin-text-warning fw-bold"></div>
                </div>
                <div class="modal-footer admin-modal-footer py-2 justify-content-center">
                    <button type="button" class="btn admin-btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn admin-btn-danger btn-sm" id="confirmDeleteAnime">
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
    <script src="<?= base_url('assets/js/admin/anime_manage.js') ?>"></script>
</body>
</html>
