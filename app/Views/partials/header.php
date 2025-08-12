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
                             <?php if (!empty(session('user_profile'))): ?>
                                 <img id="navbarAvatar" src="<?= esc(session('user_profile')) ?>" alt="User" class="user-avatar rounded-circle border">
                             <?php else: ?>
                                 <div id="navbarAvatar" class="user-avatar-letter rounded-circle border d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; background: linear-gradient(45deg, #8B5CF6, #A855F7); color: white; font-size: 16px;">
                                     <?= strtoupper(substr(session('username') ?? 'U', 0, 1)) ?>
                                 </div>
                             <?php endif; ?>
                         </a>
                         <div class="dropdown-menu dropdown-menu-end p-2 shadow rounded-3 user-dropdown-menu" aria-labelledby="userDropdown">
                             <div class="mb-2 px-2">
                                 <span class="fw-bold text-purple user-dropdown-username"><?= session('username') ?></span><br>
                                 <span class="small text-secondary"><?= session('email') ?></span>
                             </div>
                             <a href="<?= base_url('account/profile') ?>" class="dropdown-item d-flex align-items-center rounded-2 text-light bg-transparent px-2 user-dropdown-btn">
                                 <i class="fas fa-user me-2 user-dropdown-icon"></i>Profile
                             </a>
                             <a href="<?= base_url('account/notifications') ?>" class="dropdown-item d-flex align-items-center rounded-2 text-light bg-transparent px-2 user-dropdown-btn position-relative">
                                 <i class="fas fa-bell me-2 user-dropdown-icon"></i>Notifications
                                 <span class="notification-badge bg-warning text-dark rounded-pill px-2 py-1 ms-auto" style="font-size: 0.7rem; min-width: 20px; display: none;">0</span>
                             </a>
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
     
     // Load notification count for navbar badge
     <?php if (session('isLoggedIn')): ?>
     document.addEventListener('DOMContentLoaded', function() {
         fetch('<?= base_url('api/notifications/unread-count') ?>')
             .then(response => response.json())
             .then(data => {
                 const badge = document.querySelector('.notification-badge');
                 if (badge && data.count > 0) {
                     badge.textContent = data.count;
                     badge.style.display = 'inline';
                 }
             })
             .catch(error => console.error('Error loading notification count:', error));
     });
     <?php endif; ?>
 </script>
 <script src="<?= base_url('assets/js/search.js') ?>"></script>
<script src="<?= base_url('assets/js/auth.js') ?>"></script>
