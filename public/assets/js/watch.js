document.addEventListener('DOMContentLoaded', function() {
    // Function to extract YouTube video ID from URL
    function extractYouTubeID(url) {
        const patterns = [
            /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/,
            /youtube\.com\/v\/([^&\n?#]+)/,
            /youtube\.com\/watch\?.*v=([^&\n?#]+)/
        ];
        
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match && match[1]) {
                return match[1];
            }
        }
        return null;
    }

    // Function to generate appropriate player HTML
    function generatePlayerHTML(url, sourceType) {
        // Use sourceType to determine player
        if (sourceType === 'YouTube') {
            const videoId = extractYouTubeID(url);
            if (videoId) {
                const currentOrigin = window.location.origin;
                return `
                    <div class="plyr__video-embed" id="player">
                        <iframe
                            src="https://www.youtube.com/embed/${videoId}?origin=${currentOrigin}&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1"
                            allowfullscreen
                            allowtransparency
                            allow="autoplay"
                            style="width: 100%; height: 100%; border: none;">
                        </iframe>
                    </div>
                `;
            }
        } else if (sourceType === 'Gdrive' || sourceType === 'Blogger') {
            return `
                <div class="custom-iframe-container" id="player">
                    <iframe
                        src="${url}"
                        allowfullscreen
                        allowtransparency
                        allow="autoplay">
                    </iframe>
                </div>
            `;
        } else {
            const posterUrl = window.animeData && window.animeData.poster ? window.animeData.poster : '';
            return `
                <video 
                    id="player" 
                    playsinline 
                    controls 
                    data-poster="${posterUrl}"
                    crossorigin="anonymous"
                    class="plyr-video">
                    <source src="${url}" type="video/mp4" />
                    <p>Your browser doesn't support HTML5 video.</p>
                </video>
            `;
        }
    }

    // Function to initialize player based on URL type
    function initializePlayer(url, sourceType) {
        const playerContainer = document.querySelector('.player-container');
        playerContainer.innerHTML = '';
        const playerHTML = generatePlayerHTML(url, sourceType);
        playerContainer.innerHTML = playerHTML;
        if (sourceType === 'Gdrive' || sourceType === 'Blogger') {
            return {
                on: function(event, callback) {
                    if (event === 'ready') setTimeout(callback, 100);
                },
                once: function(event, callback) {
                    if (event === 'ready') setTimeout(callback, 100);
                },
                togglePlay: function() {},
                rewind: function() {},
                forward: function() {},
                fullscreen: { toggle: function() {} },
                muted: false
            };
        } else {
            const player = new Plyr('#player', getPlayerConfig(url, sourceType));
            setupPlayerEvents(player);
            return player;
        }
    }

    // Function to get player configuration based on URL type
    function getPlayerConfig(url, sourceType) {
        const baseConfig = {
            controls: [
                'play-large',
                'restart',
                'rewind',
                'play',
                'fast-forward',
                'progress',
                'current-time',
                'duration',
                'mute',
                'volume',
                'captions',
                'settings',
                'pip',
                'airplay',
                'fullscreen'
            ],
            settings: ['quality', 'speed', 'loop'],
            speed: {
                selected: 1,
                options: [0.5, 0.75, 1, 1.25, 1.5, 2]
            },
            ratio: '16:9',
            fullscreen: {
                enabled: true,
                fallback: true,
                iosNative: true
            },
            storage: {
                enabled: true,
                key: 'plyr'
            }
        };

        if (sourceType === 'YouTube') {
            return {
                ...baseConfig,
                youtube: {
                    noCookie: false,
                    rel: 0,
                    showinfo: 0,
                    iv_load_policy: 3,
                    modestbranding: 1
                }
            };
        } else {
            return {
                ...baseConfig,
                quality: {
                    default: 720,
                    options: [1080, 720, 480, 360],
                    forced: true,
                    onChange: (quality) => {
                        console.log('Quality changed to:', quality);
                    }
                }
            };
        }
    }

    // Function to set up player events
    function setupPlayerEvents(player) {
        player.on('ready', () => {
            console.log('Player is ready');
            // Force controls to be visible
            const controls = document.querySelector('.plyr__controls');
            if (controls) {
                controls.style.opacity = '1';
                controls.style.visibility = 'visible';
                controls.style.zIndex = '10';
            }
        });

        player.on('error', (event) => {
            console.error('Player error:', event);
        });

        // Auto-play next episode when current ends
        player.on('ended', function() {
            if (autoNext && window.animeData) {
                const currentEp = window.animeData.currentEpisode;
                const totalEps = window.animeData.totalEpisodes;
                
                if (currentEp < totalEps) {
                    // Fetch and play next episode
                    fetchAndPlayEpisode(currentEp + 1);
                }
            }
        });
    }

    // Initialize player with current episode URL
    let player = (window.animeData && window.animeData.currentEpisodeUrl)
        ? initializePlayer(window.animeData.currentEpisodeUrl, window.animeData.sourceType)
        : null;

    // Handle theater mode toggle
    const theaterBtn = document.querySelector('[title="Toggle Theater Mode"]');
    if (theaterBtn) {
        theaterBtn.addEventListener('click', function() {
            const videoSection = document.querySelector('.video-player-section');
            const sidebar = document.querySelector('.watch-sidebar').parentElement;
            
            if (videoSection.classList.contains('theater-mode')) {
                // Exit theater mode
                videoSection.classList.remove('theater-mode');
                sidebar.style.display = 'block';
                videoSection.parentElement.classList.remove('col-12');
                videoSection.parentElement.classList.add('col-lg-9');
                this.querySelector('i').className = 'fas fa-expand';
                this.title = 'Toggle Theater Mode';
            } else {
                // Enter theater mode
                videoSection.classList.add('theater-mode');
                sidebar.style.display = 'none';
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
            // Toggle favorite status
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

    // Dynamic episode switching without page reload
    const episodeItems = document.querySelectorAll('.episode-btn');
    episodeItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            
            const episodeNumber = parseInt(this.textContent.trim());
            
            // If it's the current episode, don't reload
            if (episodeNumber === window.animeData.currentEpisode) {
                return;
            }
            
            // Fetch episode URL from database
            fetchAndPlayEpisode(episodeNumber);
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Only apply keyboard shortcuts if player supports them (not for Google Drive/Blogger)
        if (window.animeData && window.animeData.sourceType && (window.animeData.sourceType === 'Gdrive' || window.animeData.sourceType === 'Blogger')) {
            return; // Skip keyboard shortcuts for iframe players
        }
        // ...existing code for keyboard shortcuts...
        // Space bar to play/pause
        if (e.code === 'Space' && !e.target.matches('input, textarea')) {
            e.preventDefault();
            if (player && player.togglePlay) {
                player.togglePlay();
            }
        }
        // Arrow keys for seeking
        if (e.code === 'ArrowLeft') {
            e.preventDefault();
            if (player && player.rewind) {
                player.rewind(10);
            }
        }
        if (e.code === 'ArrowRight') {
            e.preventDefault();
            if (player && player.forward) {
                player.forward(10);
            }
        }
        // F key for fullscreen
        if (e.code === 'KeyF') {
            e.preventDefault();
            if (player && player.fullscreen && player.fullscreen.toggle) {
                player.fullscreen.toggle();
            }
        }
        // M key for mute
        if (e.code === 'KeyM') {
            e.preventDefault();
            if (player && typeof player.muted !== 'undefined') {
                player.muted = !player.muted;
            }
        }
    });

    // Update page title when episode changes
    if (window.animeData) {
        document.title = `${window.animeData.slug.replace(/-/g, ' ')} - Episode ${window.animeData.currentEpisode} | RioWave`;
        // Initialize navigation buttons with current episode
        updateNavigationButtons(window.animeData.currentEpisode);
    }

    // Simple function to fetch episode URL and play
    function fetchAndPlayEpisode(episodeNumber) {
        // Show loading
        const loadingOverlay = document.createElement('div');
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
        loadingOverlay.innerHTML = '<div class="spinner-border me-2"></div>Loading Episode ' + episodeNumber + '...';
        const playerContainer = document.querySelector('.player-container');
        playerContainer.appendChild(loadingOverlay);

        const animetitle = document.querySelector('.anime-title').textContent.trim();
        const episodeNum = document.querySelector('.episode-indicator').textContent.match(/Episode (\d+)/)[1];

        fetch(`${window.animeData.baseUrl}api/episode-url`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `title=${animetitle}&episode=${episodeNum}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.url) {
                // Use data.sourceType if available, fallback to 'Unknown SourceType'
                const sourceType = data.sourceType || 'Unknown SourceType';
                player = initializePlayer(data.url, sourceType);
                updateEpisodeUI(episodeNumber);
                player.once('ready', () => {
                    if (loadingOverlay && loadingOverlay.parentNode) {
                        loadingOverlay.remove();
                    }
                });
                window.animeData.currentEpisodeUrl = data.url;
                window.animeData.sourceType = sourceType;
            } else {
                if (loadingOverlay && loadingOverlay.parentNode) {
                    loadingOverlay.remove();
                }
                alert('Episode not available');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loadingOverlay && loadingOverlay.parentNode) {
                loadingOverlay.remove();
            }
            window.location.href = `${window.animeData.baseUrl}watch/${window.animeData.slug}/${episodeNumber}`;
        });
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

        // Update navigation buttons with correct URLs
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
            
            // Add click handler for AJAX switching
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetchAndPlayEpisode(currentEpisode - 1);
            });
            
            episodeNavigation.appendChild(prevBtn);
        }

        // Add next button if not last episode
        if (currentEpisode < window.animeData.totalEpisodes) {
            const nextBtn = document.createElement('a');
            nextBtn.href = `${window.animeData.baseUrl}watch/${window.animeData.slug}/${currentEpisode + 1}`;
            nextBtn.className = 'nav-episode-btn next-btn';
            nextBtn.innerHTML = 'Next<i class="fas fa-chevron-right ms-1"></i>';
            
            // Add click handler for AJAX switching
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetchAndPlayEpisode(currentEpisode + 1);
            });
            
            episodeNavigation.appendChild(nextBtn);
        }
    }

    // Use event delegation for navigation buttons to handle both initial and dynamically created buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.nav-episode-btn')) {
            e.preventDefault();
            
            const btn = e.target.closest('.nav-episode-btn');
            const url = btn.href;
            const episodeNumber = url.match(/\/(\d+)$/)?.[1];
            
            if (episodeNumber && parseInt(episodeNumber) !== window.animeData.currentEpisode) {
                fetchAndPlayEpisode(parseInt(episodeNumber));
            }
        }
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(event) {
        // Extract episode number from current URL
        const currentUrl = window.location.pathname;
        const episodeMatch = currentUrl.match(/\/(\d+)$/);
        
        if (episodeMatch) {
            const episodeNumber = parseInt(episodeMatch[1]);
            
            if (episodeNumber !== window.animeData.currentEpisode) {
                fetchAndPlayEpisode(episodeNumber);
            }
        }
    });


});
