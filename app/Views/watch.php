<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($anime['title']) ?> - Watch Online | RioWave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Plyr.io CSS -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    
    <!-- Custom CSS Files -->
    <link href="<?= base_url('assets/css/variables.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/header.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/carousel.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/components.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/responsive.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/watch.css') ?>" rel="stylesheet">
</head>

<body>
    <?php
    // Load the URL helper
    helper('url');
    ?>
    <?= $this->include('partials/header') ?>

    <!-- Watch Section -->
    <section class="watch-section">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Main Content -->
                <div class="col-lg-9 col-xl-10">
                    <!-- Video Player Container -->
                    <div class="video-player-section">
                        <!-- Player Header -->
                        <div class="player-header">
                            <div class="player-title">
                                <h3 class="anime-title"><?= esc($anime['title']) ?></h3>
                                <span class="episode-indicator">Episode <?= $currentEpisode ?></span>
                            </div>
                            <div class="player-controls">
                                <button class="control-btn" title="Auto Next">
                                    <i class="fas fa-step-forward"></i>
                                </button>
                                <button class="control-btn" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="control-btn" title="Report">
                                    <i class="fas fa-flag"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Video Player -->
                        <div class="player-container">
                            <video 
                                id="player" 
                                playsinline 
                                controls 
                                data-poster="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : '' ?>"
                                crossorigin="anonymous"
                                class="plyr-video">
                                <?php if (!empty($episodes) && isset($episodes[$currentEpisode - 1])): ?>
                                    <source src="<?= esc($episodes[$currentEpisode - 1]['url']) ?>" type="video/mp4" />
                                <?php endif; ?>
                                <p>Your browser doesn't support HTML5 video.</p>
                            </video>
                        </div>

                        <!-- Player Footer -->
                        <div class="player-footer">
                            <div class="source-info">
                                <span class="source-name">HD Stream</span>
                                <button class="change-source-btn">
                                    <i class="fas fa-exchange-alt me-1"></i>Change
                                </button>
                            </div>
                            <div class="episode-navigation">
                                <?php if ($currentEpisode > 1): ?>
                                    <a href="<?= base_url('watch/' . $slug . '/' . ($currentEpisode - 1)) ?>" 
                                       class="nav-episode-btn prev-btn">
                                        <i class="fas fa-chevron-left me-1"></i>Previous
                                    </a>
                                <?php endif; ?>
                                <?php if ($currentEpisode < count($episodes)): ?>
                                    <a href="<?= base_url('watch/' . $slug . '/' . ($currentEpisode + 1)) ?>" 
                                       class="nav-episode-btn next-btn">
                                        Next<i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Episodes Grid -->
                    <?php if (!empty($episodes) && count($episodes) > 1): ?>
                        <div class="episodes-section">
                            <h4 class="section-title">Episodes</h4>
                            <div class="episodes-scroll-container">
                                <div class="episodes-grid">
                                    <?php foreach ($episodes as $episode): ?>
                                        <a href="<?= base_url('watch/' . $slug . '/' . $episode['episode_number']) ?>"
                                           class="episode-btn <?= $episode['episode_number'] == $currentEpisode ? 'active' : '' ?>">
                                            <?= $episode['episode_number'] ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Plyr.io JavaScript -->
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/watch.js') ?>"></script>
    
    <script>
        // Pass minimal data to JavaScript
        window.animeData = {
            title: '<?= esc($anime['title']) ?>',
            slug: '<?= $slug ?>',
            currentEpisode: <?= $currentEpisode ?>,
            totalEpisodes: <?= count($episodes) ?>,
            baseUrl: '<?= base_url() ?>',
            currentEpisodeUrl: '<?= !empty($episodes) && isset($episodes[$currentEpisode - 1]) ? esc($episodes[$currentEpisode - 1]['url']) : '' ?>'
        };
    </script>
</body>
</html>

<?php
// Helper function to get badge class based on anime type
function getBadgeClass($type) {
    $class = strtolower(trim($type));
    switch ($class) {
        case 'tv':
            return 'badge-tv';
        case 'movie':
            return 'badge-movie';
        case 'ova':
            return 'badge-ova';
        case 'ona':
            return 'badge-ona';
        case 'special':
            return 'badge-special';
        case 'completed':
            return 'badge-completed';
        default:
            return 'badge-default';
    }
}
?>
