 <?php
// Load the URL helper
helper('url');
?>
<!-- Header -->
<header class="header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <a href="<?= base_url() ?>" class="navbar-brand">
                <div class="logo-icon">
                    <i class="fas fa-play"></i>
                </div>
                <span>rio<span style="color: var(--primary-purple);">wave</span></span>
            </a>

            <!-- Search Bar -->
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search anime..." id="searchInput" autocomplete="off">
                <div class="search-results" id="searchResults" style="display: none;">
                    <!-- Search results will be populated by JavaScript -->
                </div>
            </div>

            <!-- Header Actions -->
            <div class="header-actions">
                <button class="filter-btn">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <button class="safe-btn">SAFE</button>
                <button class="safe-btn">NSFW</button>
                <button class="info-btn">Info</button>
                <img src="https://via.placeholder.com/36x36/8B5CF6/ffffff?text=U" alt="User" class="user-avatar">
            </div>
        </div>
    </div>
</header>

<!-- Search JavaScript -->
<script>
    window.baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/search.js') ?>"></script>
