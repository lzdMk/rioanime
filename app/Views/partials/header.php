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
                 <a href="<?= base_url('random') ?>" class="random-btn" style="text-decoration:none;">
                     <i class="fas fa-random me-1"></i>Random
                 </a>
                 <?php if (session('isLoggedIn')): ?>
                     <div class="dropdown">
                         <a href="#" class="d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                             <img src="https://via.placeholder.com/36x36/8B5CF6/ffffff?text=U" alt="User" class="user-avatar rounded-circle border">
                         </a>
                         <div class="dropdown-menu dropdown-menu-end p-2 shadow rounded-3 user-dropdown-menu" aria-labelledby="userDropdown">
                             <div class="mb-2 px-2">
                                 <span class="fw-bold text-purple user-dropdown-username"><?= session('username') ?></span><br>
                                 <span class="small text-secondary"><?= session('email') ?></span>
                             </div>
                             <button class="dropdown-item d-flex align-items-center rounded-2 text-light bg-transparent px-2 user-dropdown-btn">
                                 <i class="fas fa-user me-2 user-dropdown-icon"></i>Profile
                             </button>
                             <button class="dropdown-item d-flex align-items-center rounded-2 text-light bg-transparent px-2 user-dropdown-btn">
                                 <i class="fas fa-bell me-2 user-dropdown-icon"></i>Notification
                             </button>
                             <button class="dropdown-item d-flex align-items-center rounded-2 text-light bg-transparent px-2 user-dropdown-btn">
                                 <i class="fas fa-cog me-2 user-dropdown-icon"></i>Settings
                             </button>
                             <div class="mt-3 text-end px-2">
                                 <button id="logoutBtn" class="btn btn-sm btn-outline-light fw-bold">Logout <i class="fas fa-arrow-right ms-1"></i></button>
                             </div>
                         </div>
                     </div>
             </div>
         <?php else: ?>
             <button class="login-btn">
                 <i class="fas fa-user me-1"></i>Login
             </button>
         <?php endif; ?>
         </div>
     </div>
     </div>
 </header>

 <!-- Search JavaScript -->
 <script>
     window.baseUrl = '<?= base_url() ?>';
 </script>
 <script src="<?= base_url('assets/js/search.js') ?>"></script>
