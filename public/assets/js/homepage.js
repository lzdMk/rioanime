// Homepage JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
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
    console.log('Loading top anime for period:', period);
    // Here you would typically make an AJAX call to load different data
    // For now, we'll just log the action
    
    // Example AJAX call structure:
    /*
    fetch(`/api/top-anime/${period}`)
        .then(response => response.json())
        .then(data => {
            updateTopAnimeList(data);
        })
        .catch(error => console.error('Error loading top anime:', error));
    */
}

// Function to update top anime list (placeholder)
function updateTopAnimeList(data) {
    const topAnimeList = document.getElementById('topAnimeList');
    // Update the list with new data
    console.log('Updating top anime list with:', data);
}
