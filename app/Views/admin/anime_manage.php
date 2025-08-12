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
                                        <div class="col-md-4">
                                            <input type="text" class="form-control admin-form-control" id="searchInput" placeholder="Search by title or genre...">
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
                                                <option value="Completed">Completed</option>
                                                <option value="Ongoing">Ongoing</option>
                                                <option value="Upcoming">Upcoming</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-select admin-form-control" id="languageFilter">
                                                <option value="">All Languages</option>
                                                <option value="Japanese">Japanese</option>
                                                <option value="English">English</option>
                                                <option value="Chinese">Chinese</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn admin-btn-outline w-100" id="refreshBtn">
                                                <i class="fas fa-sync-alt"></i> Refresh
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Anime Table -->
                                    <div class="table-responsive">
                                        <table class="table admin-table table-hover" id="animeTable">
                                            <thead class="admin-table-header">
                                                <tr>
                                                    <th>Image</th>
                                                    <th>Title</th>
                                                    <th>Type</th>
                                                    <th>Episodes</th>
                                                    <th>Status</th>
                                                    <th>Rating</th>
                                                    <th>Language</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="admin-table-body">
                                                <?php if (!empty($anime_list)): ?>
                                                    <?php foreach ($anime_list as $anime): ?>
                                                        <tr data-anime-id="<?= $anime['anime_id'] ?>">
                                                            <td class="text-center">
                                                                <img src="<?= esc($anime['backgroundImage']) ?>" alt="<?= esc($anime['title']) ?>" class="admin-anime-thumb">
                                                            </td>
                                                            <td class="admin-text-primary fw-bold"><?= esc($anime['title']) ?></td>
                                                            <td>
                                                                <span class="badge admin-badge-<?= strtolower($anime['type']) === 'movie' ? 'warning' : 'info' ?>">
                                                                    <?= esc($anime['type']) ?>
                                                                </span>
                                                            </td>
                                                            <td class="admin-text-muted"><?= $anime['total_ep'] ? esc($anime['total_ep']) : 'N/A' ?></td>
                                                            <td>
                                                                <span class="badge admin-badge-<?= strtolower($anime['status']) === 'completed' ? 'success' : (strtolower($anime['status']) === 'ongoing' ? 'primary' : 'secondary') ?>">
                                                                    <?= esc($anime['status']) ?>
                                                                </span>
                                                            </td>
                                                            <td class="admin-text-muted"><?= $anime['ratings'] ? esc($anime['ratings']) : 'N/A' ?></td>
                                                            <td class="admin-text-muted"><?= esc($anime['language']) ?></td>
                                                            <td>
                                                                <div class="btn-group admin-action-buttons" role="group">
                                                                    <button type="button" class="btn admin-btn-info btn-sm view-anime" data-id="<?= $anime['anime_id'] ?>" title="View Details">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    <button type="button" class="btn admin-btn-warning btn-sm edit-anime" data-id="<?= $anime['anime_id'] ?>" title="Edit Anime">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button type="button" class="btn admin-btn-danger btn-sm delete-anime" data-id="<?= $anime['anime_id'] ?>" title="Delete Anime">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center admin-text-muted py-4">
                                                            <i class="fas fa-film fa-3x mb-3 opacity-50"></i>
                                                            <p class="mb-0">No anime found</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <?php if (isset($pager)): ?>
                                        <div class="admin-pagination-wrapper">
                                            <div class="admin-pagination-info">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Showing anime results with modern pagination
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <?= $pager->links() ?>
                                            </div>
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

    <!-- Add Anime Modal -->
    <div class="modal fade" id="addAnimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title admin-text-primary"><i class="fas fa-plus me-2"></i>Add New Anime</h5>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addAnimeForm">
                    <div class="modal-body admin-modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Title *</label>
                                    <input type="text" class="form-control admin-form-control" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Type *</label>
                                    <select class="form-select admin-form-control" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="TV">TV Series</option>
                                        <option value="Movie">Movie</option>
                                        <option value="OVA">OVA</option>
                                        <option value="Special">Special</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Language *</label>
                                    <select class="form-select admin-form-control" name="language" required>
                                        <option value="">Select Language</option>
                                        <option value="Japanese">Japanese</option>
                                        <option value="English">English</option>
                                        <option value="Chinese">Chinese</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Total Episodes</label>
                                    <input type="number" class="form-control admin-form-control" name="total_ep" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Status *</label>
                                    <select class="form-select admin-form-control" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Upcoming">Upcoming</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Rating</label>
                                    <input type="text" class="form-control admin-form-control" name="ratings" placeholder="e.g., 8.5">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Genres</label>
                            <input type="text" class="form-control admin-form-control" name="genres" placeholder="Action, Adventure, Comedy">
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Studios</label>
                            <input type="text" class="form-control admin-form-control" name="studios" placeholder="Studio name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Background Image URL</label>
                            <input type="url" class="form-control admin-form-control" name="backgroundImage">
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">URLs (streaming links)</label>
                            <textarea class="form-control admin-form-control" name="urls" rows="3" placeholder="One URL per line"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Synopsis</label>
                            <textarea class="form-control admin-form-control" name="synopsis" rows="4" placeholder="Brief description of the anime"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer admin-modal-footer">
                        <button type="button" class="btn admin-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn admin-btn-primary">Add Anime</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Anime Modal -->
    <div class="modal fade" id="editAnimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title admin-text-warning"><i class="fas fa-edit me-2"></i>Edit Anime</h5>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAnimeForm">
                    <input type="hidden" id="editAnimeId" name="anime_id">
                    <div class="modal-body admin-modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Title *</label>
                                    <input type="text" class="form-control admin-form-control" id="editTitle" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Type *</label>
                                    <select class="form-select admin-form-control" id="editType" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="TV">TV Series</option>
                                        <option value="Movie">Movie</option>
                                        <option value="OVA">OVA</option>
                                        <option value="Special">Special</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Language *</label>
                                    <select class="form-select admin-form-control" id="editLanguage" name="language" required>
                                        <option value="">Select Language</option>
                                        <option value="Japanese">Japanese</option>
                                        <option value="English">English</option>
                                        <option value="Chinese">Chinese</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Total Episodes</label>
                                    <input type="number" class="form-control admin-form-control" id="editTotalEp" name="total_ep" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Status *</label>
                                    <select class="form-select admin-form-control" id="editStatus" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Upcoming">Upcoming</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label admin-form-label">Rating</label>
                                    <input type="text" class="form-control admin-form-control" id="editRatings" name="ratings" placeholder="e.g., 8.5">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Genres</label>
                            <input type="text" class="form-control admin-form-control" id="editGenres" name="genres" placeholder="Action, Adventure, Comedy">
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Studios</label>
                            <input type="text" class="form-control admin-form-control" id="editStudios" name="studios" placeholder="Studio name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Background Image URL</label>
                            <input type="url" class="form-control admin-form-control" id="editBackgroundImage" name="backgroundImage">
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">URLs (streaming links)</label>
                            <textarea class="form-control admin-form-control" id="editUrls" name="urls" rows="3" placeholder="One URL per line"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label admin-form-label">Synopsis</label>
                            <textarea class="form-control admin-form-control" id="editSynopsis" name="synopsis" rows="4" placeholder="Brief description of the anime"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer admin-modal-footer">
                        <button type="button" class="btn admin-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn admin-btn-warning">Update Anime</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Anime Modal -->
    <div class="modal fade" id="viewAnimeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title admin-text-info"><i class="fas fa-film me-2"></i>Anime Details</h5>
                    <button type="button" class="btn-close admin-btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body admin-modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div id="viewAnimeImage" class="mb-3"></div>
                            <h5 id="viewAnimeTitle" class="admin-text-primary"></h5>
                            <div class="mb-2">
                                <span id="viewAnimeType" class="badge me-1"></span>
                                <span id="viewAnimeStatus" class="badge"></span>
                            </div>
                            <div id="viewAnimeRating" class="admin-text-warning"></div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless admin-info-table">
                                <tr>
                                    <th class="admin-text-muted">Language:</th>
                                    <td id="viewAnimeLanguage" class="admin-text-primary"></td>
                                </tr>
                                <tr>
                                    <th class="admin-text-muted">Episodes:</th>
                                    <td id="viewAnimeEpisodes" class="admin-text-primary"></td>
                                </tr>
                                <tr>
                                    <th class="admin-text-muted">Genres:</th>
                                    <td id="viewAnimeGenres" class="admin-text-primary"></td>
                                </tr>
                                <tr>
                                    <th class="admin-text-muted">Studios:</th>
                                    <td id="viewAnimeStudios" class="admin-text-primary"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="admin-text-muted">Synopsis:</h6>
                            <div id="viewAnimeSynopsis" class="admin-text-primary p-3 rounded" style="background: var(--darker-color); border: 1px solid var(--border-color);"></div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="admin-text-muted">Streaming URLs:</h6>
                            <div id="viewAnimeUrls" class="admin-text-primary"></div>
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
    <script src="<?= base_url('assets/js/admin/anime.js') ?>"></script>
</body>
</html>
