<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($anime['title']) ?> - Watch Online | RioWave</title>
    <?= $this->include('partials/custom_link') ?>
    <link href="<?= base_url('assets/css/watch.css') ?>" rel="stylesheet">
</head>

<body>
    <?php helper('url'); ?>
    <?= $this->include('partials/header') ?>

    <!-- Watch Section -->
    <section class="watch-section">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Main Content -->
                <div class="col-12 col-lg-9 col-xl-10">
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
                            <iframe 
                                id="embedded-player"
                                src="<?= base_url('embeded/watch/' . $anime['anime_id'] . '/' . $currentEpisode) ?>"
                                allowfullscreen
                                allowtransparency
                                allow="autoplay; encrypted-media"
                                frameborder="0"
                                style="width: 100%; height: 100%; border: none;">
                            </iframe>
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
                    <?php if (!empty($episodes)): ?>
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

    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/watch.js') ?>"></script>
    <script>
        window.animeData = {
            animeId: <?= json_encode($anime['anime_id']) ?>,
            title: <?= json_encode($jsData['title']) ?>,
            currentEpisode: <?= json_encode($jsData['currentEpisode']) ?>,
            totalEpisodes: <?= json_encode($jsData['totalEpisodes']) ?>,
            slug: <?= json_encode($jsData['slug']) ?>,
            baseUrl: <?= json_encode(base_url()) ?>
        };
    </script>
</body>
</html>
