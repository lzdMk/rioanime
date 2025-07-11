document.addEventListener('DOMContentLoaded', function() {
    // Simple embedded iframe system
    
    // Handle episode switching
    const episodeItems = document.querySelectorAll('.episode-btn');
    episodeItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const episodeNumber = parseInt(this.textContent.trim());
            
            // If it's the current episode, don't reload
            if (episodeNumber === window.animeData.currentEpisode) {
                return;
            }
            
            // Switch to new episode using embedded URL
            switchToEpisode(episodeNumber);
        });
    });

    // Handle navigation button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.nav-episode-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.nav-episode-btn');
            const href = btn.getAttribute('href');
            const episodeMatch = href.match(/\/(\d+)$/);
            
            if (episodeMatch) {
                const episodeNumber = parseInt(episodeMatch[1]);
                switchToEpisode(episodeNumber);
            }
        }
    });

    // Function to switch episodes
    function switchToEpisode(episodeNumber) {
        // Update the iframe source
        const iframe = document.getElementById('embedded-player');
        if (!iframe) return;
        
        const newEmbeddedUrl = `${window.animeData.baseUrl}embeded/watch/${window.animeData.animeId}/${episodeNumber}`;
        
        // Show loading indicator
        showLoading();
        
        // Update iframe src
        iframe.src = newEmbeddedUrl;
        
        // Update UI
        updateEpisodeUI(episodeNumber);
        
        // Hide loading after iframe loads
        iframe.onload = function() {
            hideLoading();
        };
    }

    // Update UI when episode changes
    function updateEpisodeUI(episodeNumber) {
        // Update episode buttons
        document.querySelectorAll('.episode-btn').forEach(btn => {
            if (parseInt(btn.textContent.trim()) === episodeNumber) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Update episode indicator
        const episodeIndicator = document.querySelector('.episode-indicator');
        if (episodeIndicator) {
            episodeIndicator.textContent = 'Episode ' + episodeNumber;
        }

        // Update page title
        document.title = `${window.animeData.title} - Episode ${episodeNumber} | RioWave`;

        // Update current episode
        window.animeData.currentEpisode = episodeNumber;

        // Update browser URL
        const newUrl = `${window.animeData.baseUrl}watch/${window.animeData.slug}/${episodeNumber}`;
        window.history.pushState({}, '', newUrl);

        // Update navigation buttons
        updateNavigationButtons(episodeNumber);
    }

    // Function to update navigation buttons
    function updateNavigationButtons(currentEpisode) {
        const episodeNavigation = document.querySelector('.episode-navigation');
        if (!episodeNavigation) return;

        // Clear existing buttons
        episodeNavigation.innerHTML = '';

        // Add previous button if not first episode
        if (currentEpisode > 1) {
            const prevBtn = document.createElement('a');
            prevBtn.href = `${window.animeData.baseUrl}watch/${window.animeData.slug}/${currentEpisode - 1}`;
            prevBtn.className = 'nav-episode-btn prev-btn';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left me-1"></i>Previous';
            
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                switchToEpisode(currentEpisode - 1);
            });
            
            episodeNavigation.appendChild(prevBtn);
        }

        // Add next button if not last episode
        if (currentEpisode < window.animeData.totalEpisodes) {
            const nextBtn = document.createElement('a');
            nextBtn.href = `${window.animeData.baseUrl}watch/${window.animeData.slug}/${currentEpisode + 1}`;
            nextBtn.className = 'nav-episode-btn next-btn';
            nextBtn.innerHTML = 'Next<i class="fas fa-chevron-right ms-1"></i>';
            
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                switchToEpisode(currentEpisode + 1);
            });
            
            episodeNavigation.appendChild(nextBtn);
        }
    }

    // Loading functions
    function showLoading() {
        let loadingOverlay = document.querySelector('.video-loading-overlay');
        if (!loadingOverlay) {
            loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'video-loading-overlay';
            loadingOverlay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 100;
                color: #fff;
                font-size: 1rem;
            `;
            loadingOverlay.innerHTML = '<div class="spinner-border me-2"></div>Loading Episode...';
            document.querySelector('.player-container').appendChild(loadingOverlay);
        }
        loadingOverlay.style.display = 'flex';
    }

    function hideLoading() {
        const loadingOverlay = document.querySelector('.video-loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }

    // Handle theater mode toggle
    const theaterBtn = document.querySelector('[title="Toggle Theater Mode"]');
    if (theaterBtn) {
        theaterBtn.addEventListener('click', function() {
            const videoSection = document.querySelector('.video-player-section');
            const sidebar = document.querySelector('.watch-sidebar');
            
            if (videoSection.classList.contains('theater-mode')) {
                // Exit theater mode
                videoSection.classList.remove('theater-mode');
                if (sidebar) sidebar.parentElement.style.display = 'block';
                videoSection.parentElement.classList.remove('col-12');
                videoSection.parentElement.classList.add('col-lg-9');
                this.querySelector('i').className = 'fas fa-expand';
                this.title = 'Toggle Theater Mode';
            } else {
                // Enter theater mode
                videoSection.classList.add('theater-mode');
                if (sidebar) sidebar.parentElement.style.display = 'none';
                videoSection.parentElement.classList.remove('col-lg-9');
                videoSection.parentElement.classList.add('col-12');
                this.querySelector('i').className = 'fas fa-compress';
                this.title = 'Exit Theater Mode';
            }
        });
    }

    // Handle auto-next functionality
    const autoNextBtn = document.querySelector('[title="Auto Next"]');
    let autoNext = false;
    
    if (autoNextBtn) {
        autoNextBtn.addEventListener('click', function() {
            autoNext = !autoNext;
            this.classList.toggle('active');
            this.style.background = autoNext ? 'var(--accent-color)' : '';
        });
    }

    // Handle favorite button
    const favoriteBtn = document.querySelector('.action-btn-full');
    if (favoriteBtn && favoriteBtn.textContent.includes('Favorites')) {
        favoriteBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const text = this.querySelector('span') || this;
            
            if (icon.classList.contains('fas')) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                text.innerHTML = '<i class="far fa-heart me-2"></i>Add to Favorites';
                this.classList.remove('btn-danger');
                this.classList.add('btn-primary');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                text.innerHTML = '<i class="fas fa-heart me-2"></i>Remove from Favorites';
                this.classList.remove('btn-primary');
                this.classList.add('btn-danger');
            }
        });
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(event) {
        const currentUrl = window.location.pathname;
        const episodeMatch = currentUrl.match(/\/(\d+)$/);
        
        if (episodeMatch) {
            const episodeNumber = parseInt(episodeMatch[1]);
            if (episodeNumber !== window.animeData.currentEpisode) {
                switchToEpisode(episodeNumber);
            }
        }
    });

    // Initialize navigation buttons with current episode
    if (window.animeData) {
        updateNavigationButtons(window.animeData.currentEpisode);
    }
});
