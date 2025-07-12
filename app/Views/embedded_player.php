<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    
    <!-- Plyr CSS -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #000;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            overflow: hidden;
        }
        
        .embedded-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            background: #000;
        }
        
        .player-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        /* Plyr customization for embedded player */
        .plyr {
            width: 100%;
            height: 100%;
        }
        
        .plyr__video-wrapper {
            height: 100%;
        }
        
        .plyr__video-embed iframe,
        .custom-iframe-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .plyr video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            z-index: 1000;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #333;
            border-top: 3px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error-message {
            color: #ff6b6b;
            text-align: center;
            padding: 20px;
            font-size: 16px;
        }
        
        /* Custom iframe container */
        .custom-iframe-container {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        /* Hide Plyr poster for better loading */
        .plyr__poster {
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body>
    <div class="embedded-container">
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
            Loading video...
        </div>
        
        <div class="player-wrapper" id="playerContainer">
            <!-- Player will be inserted here -->
        </div>
    </div>

    <!-- Plyr JS -->
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    
    <script>
        // Video data from PHP
        const videoUrl = <?= json_encode($videoUrl) ?>;
        const sourceType = <?= json_encode($sourceType) ?>;
        const animeTitle = <?= json_encode($anime['title']) ?>;
        const episodeNumber = <?= json_encode($episodeNumber) ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            const playerContainer = document.getElementById('playerContainer');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            function hideLoading() {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }
            
            function showError(message) {
                if (loadingOverlay) {
                    loadingOverlay.innerHTML = `<div class="error-message">${message}</div>`;
                }
            }
            
            function generatePlayerHTML(url, type) {
                if (type === 'YouTube') {
                    // Extract YouTube ID for Plyr YouTube player
                    const youtubeId = extractYouTubeId(url);
                    if (!youtubeId) {
                        throw new Error('Invalid YouTube URL');
                    }
                    
                    return `
                        <div class="plyr__video-embed" id="player" data-plyr-provider="youtube" data-plyr-embed-id="${youtubeId}">
                        </div>
                    `;
                } else if (type === 'Gdrive' || type === 'Blogger') {
                    return `
                        <div class="custom-iframe-container" id="player">
                            <iframe
                                src="${url}"
                                allowfullscreen
                                allowtransparency
                                allow="autoplay; encrypted-media">
                            </iframe>
                        </div>
                    `;
                } else {
                    // Direct video
                    return `
                        <video 
                            id="player" 
                            playsinline 
                            controls 
                            crossorigin="anonymous"
                            style="width: 100%; height: 100%;">
                            <source src="${url}" type="video/mp4" />
                            <p>Your browser doesn't support HTML5 video.</p>
                        </video>
                    `;
                }
            }
            
            function initializePlayer() {
                try {
                    const playerHTML = generatePlayerHTML(videoUrl, sourceType);
                    playerContainer.innerHTML = playerHTML;
                    
                    // Initialize Plyr for all supported players including YouTube
                    if (sourceType !== 'Gdrive' && sourceType !== 'Blogger') {
                        let playerConfig = {
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
                            settings: ['quality', 'speed'],
                            speed: {
                                selected: 1,
                                options: [0.5, 0.75, 1, 1.25, 1.5, 2]
                            },
                            fullscreen: {
                                enabled: true,
                                fallback: true,
                                iosNative: true
                            },
                            ratio: '16:9'
                        };
                        
                        // Add YouTube specific config
                        if (sourceType === 'YouTube') {
                            playerConfig.youtube = {
                                noCookie: false,
                                rel: 0,
                                showinfo: 0,
                                iv_load_policy: 3,
                                modestbranding: 1
                            };
                        }
                        
                        const player = new Plyr('#player', playerConfig);
                        
                        player.on('ready', hideLoading);
                        player.on('error', () => showError('Video failed to load'));
                    } else {
                        // For iframe players, hide loading after a short delay
                        setTimeout(hideLoading, 1500);
                    }
                    
                } catch (error) {
                    console.error('Player initialization error:', error);
                    showError('Failed to initialize video player');
                }
            }
            
            function extractYouTubeId(url) {
                const patterns = [
                    /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/,
                    /youtube\.com\/v\/([^&\n?#]+)/,
                    /youtube\.com\/watch\?.*v=([^&\n?#]+)/
                ];
                
                for (const pattern of patterns) {
                    const match = url.match(pattern);
                    if (match) {
                        return match[1];
                    }
                }
                return null;
            }
            
            // Initialize the player
            initializePlayer();
        });
    </script>
</body>
</html>
