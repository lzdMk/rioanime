// Homepage JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal instances first
    const registerModalEl = document.getElementById('registerModal');
    const loginModalEl = document.getElementById('loginModal');
    const registerModal = registerModalEl ? new bootstrap.Modal(registerModalEl) : null;
    const loginModal = loginModalEl ? new bootstrap.Modal(loginModalEl) : null;
    
    // Auto-play carousel with custom timing
    const carousel = new bootstrap.Carousel(document.querySelector('#animeCarousel'), {
        interval: 5000,
        ride: 'carousel'
    });

    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    // Implement search logic here
                    console.log('Searching for:', searchTerm);
                    // You can redirect to search results page
                    // window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
                }
            }
        });
    }

    // Filter button functionality
    const filterBtn = document.querySelector('.filter-btn');
    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            // Implement filter modal or dropdown logic here
            console.log('Filter clicked');
            // You can show a modal with filter options
        });
    }

    // Filter tabs functionality for Recently Updated section
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            filterTabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            const filterType = this.getAttribute('data-filter');
            filterAnimeCards(filterType);
        });
    });

    // Navigation tabs functionality for Top Anime section
    const navTabs = document.querySelectorAll('.nav-tab');
    navTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all nav tabs
            navTabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            const period = this.getAttribute('data-period');
            loadTopAnime(period);
        });
    });

    // View More button functionality
    const viewMoreBtn = document.querySelector('.view-more-btn');
    if (viewMoreBtn) {
        viewMoreBtn.addEventListener('click', function() {
            // Load more anime cards
            console.log('Loading more anime...');
            // You can implement AJAX call to load more content
        });
    }

    // Anime card click functionality
    const animeCards = document.querySelectorAll('.anime-card');
    animeCards.forEach(card => {
        card.addEventListener('click', function() {
            // Get anime ID or title and redirect to anime detail page
            const animeTitle = this.querySelector('.anime-title').textContent;
            console.log('Clicked on anime:', animeTitle);
            // window.location.href = `/anime/${animeId}`;
        });
    });

    // Top anime item click functionality
    const topAnimeItems = document.querySelectorAll('.top-anime-item');
    topAnimeItems.forEach(item => {
        item.addEventListener('click', function() {
            const animeName = this.querySelector('.anime-name').textContent;
            console.log('Clicked on top anime:', animeName);
            // window.location.href = `/anime/${animeId}`;
        });
    });

    // Genre card click functionality
    const genreCards = document.querySelectorAll('.genre-card');
    genreCards.forEach(card => {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const genreName = this.querySelector('.genre-name').textContent;
            console.log('Clicked on genre:', genreName);
            // window.location.href = `/genre/${genreName.toLowerCase()}`;
        });
    });

    // Registration modal trigger
    const userAvatar = document.querySelector('.user-avatar');
    if (userAvatar) {
        userAvatar.addEventListener('click', function() {
            // Use the already defined registerModal instance
            if (registerModal) {
                registerModal.show();
            } else {
                // Fallback if the instance wasn't created yet
                const tempRegisterModal = new bootstrap.Modal(document.getElementById('registerModal'));
                tempRegisterModal.show();
            }
        });
    }

    // Modal switching between Register and Login
    // Show login modal when clicking 'Login' in registration modal
    document.querySelectorAll('.login-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (registerModal) {
                registerModal.hide();
                // Wait for modal to be fully hidden before showing login
                registerModalEl.addEventListener('hidden.bs.modal', function handler() {
                    if (loginModal) loginModal.show();
                    // Remove any leftover backdrop
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    registerModalEl.removeEventListener('hidden.bs.modal', handler);
                });
            } else if (loginModal) {
                loginModal.show();
            }
        });
    });

    // Show registration modal when clicking 'Register' in login modal
    document.querySelectorAll('.register-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (loginModal) {
                loginModal.hide();
                // Wait for modal to be fully hidden before showing registration
                loginModalEl.addEventListener('hidden.bs.modal', function handler() {
                    if (registerModal) registerModal.show();
                    // Remove any leftover backdrop
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    loginModalEl.removeEventListener('hidden.bs.modal', handler);
                });
            } else if (registerModal) {
                registerModal.show();
            }
        });
    });
});

// Function to filter anime cards by type
function filterAnimeCards(filterType) {
    const animeCards = document.querySelectorAll('#recentlyUpdated .anime-card');
    const animeGrid = document.getElementById('recentlyUpdated');
    
    // Add loading effect
    animeGrid.style.opacity = '0.5';
    animeGrid.style.transition = 'opacity 0.3s ease';
    
    setTimeout(() => {
        animeCards.forEach((card, index) => {
            const cardType = card.getAttribute('data-type');
            const cardContainer = card.closest('.anime-card-link');
            
            if (filterType === 'series' || cardType === filterType || filterType === 'all') {
                cardContainer.style.display = 'block';
                // Add staggered animation
                setTimeout(() => {
                    card.style.transform = 'translateY(0)';
                    card.style.opacity = '1';
                }, index * 50);
            } else {
                cardContainer.style.display = 'none';
            }
        });
        
        // Remove loading effect
        animeGrid.style.opacity = '1';
    }, 200);
}

// Function to load top anime by period
function loadTopAnime(period) {
    const periods = ['today', 'week', 'month'];
    periods.forEach(p => {
        const el = document.querySelector(`#topAnimeList${p.charAt(0).toUpperCase() + p.slice(1)}`);
        if (el) el.style.display = (p === period) ? 'block' : 'none';
    });
}