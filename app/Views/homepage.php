<!DOCTYPE html>
<html lang="en">
<?php
// Load the URL helper
helper('url');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RioAnime - Watch Anime Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS Files -->
    <link href="<?= base_url('assets/css/variables.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/header.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/carousel.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/components.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/responsive.css') ?>" rel="stylesheet">
</head>

<body>
    <?= $this->include('partials/header') ?>

    <?php
    // Helper function to get badge class based on anime type
    function getBadgeClass($type) {
        if (empty($type)) return 'unknown';
        
        $type = strtolower(trim($type));
        
        // Map known types to their CSS classes
        $typeMap = [
            'tv' => 'tv',
            'movie' => 'movie',
            'ova' => 'ova',
            'ona' => 'ona',
            'special' => 'special',
            'series' => 'series',
            'completed' => 'completed'
        ];
        
        return isset($typeMap[$type]) ? $typeMap[$type] : 'unknown';
    }

    // Helper function to truncate title consistently
    function truncateTitle($title, $maxLength = 35) {
        return strlen($title) > $maxLength ? substr($title, 0, $maxLength) . '...' : $title;
    }
    ?>

    <!-- Hero Carousel Section -->
    <section class="hero-section">
        <div class="container">
            <div id="animeCarousel" class="carousel slide carousel-container" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php if (!empty($featuredAnime)): ?>
                        <?php foreach ($featuredAnime as $index => $anime): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=1200&h=400&fit=crop' ?>" alt="<?= esc($anime['title']) ?>">
                                <div class="carousel-overlay">
                                    <h2 class="carousel-title"><?= esc($anime['title']) ?></h2>
                                    <p class="carousel-description">
                                        <?= !empty($anime['synopsis']) ? esc(substr($anime['synopsis'], 0, 200)) . '...' : 'No synopsis available.' ?>
                                    </p>
                                    <div class="carousel-actions">
                                        <a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="btn-watch">
                                            <i class="fas fa-play"></i>
                                            Watch Now
                                        </a>
                                        <a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="btn-more">More</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback content if no featured anime -->
                        <div class="carousel-item active">
                            <img src="https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=1200&h=400&fit=crop" alt="Featured Anime">
                            <div class="carousel-overlay">
                                <h2 class="carousel-title">No Featured Anime Available</h2>
                                <p class="carousel-description">
                                    Please add some anime to the database to see featured content.
                                </p>
                                <div class="carousel-actions">
                                    <a href="#" class="btn-watch">
                                        <i class="fas fa-play"></i>
                                        Watch Now
                                    </a>
                                    <a href="#" class="btn-more">More</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Arrows -->
                <button class="carousel-control-prev" type="button" data-bs-target="#animeCarousel" data-bs-slide="prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#animeCarousel" data-bs-slide="next">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <!-- Indicators -->
                <div class="carousel-indicators">
                    <?php if (!empty($featuredAnime)): ?>
                        <?php foreach ($featuredAnime as $index => $anime): ?>
                            <button type="button" data-bs-target="#animeCarousel" data-bs-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active"' : '' ?>></button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <button type="button" data-bs-target="#animeCarousel" data-bs-slide-to="0" class="active"></button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="bottom-section mt-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="nsfw-toggle">
                            <i class="fas fa-plus me-2"></i>
                            <span>Penambahan NSFW mode on off</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="nsfw-toggle">
                            <i class="fas fa-plus me-2"></i>
                            <span>Tab <a href="#" class="top-anime-link">Top Anime</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recently Updated Section -->
    <section class="content-section">
        <div class="container">
            <div class="row">
                <!-- Main Content Column -->
                <div class="col-lg-9">
                    <!-- Section Header with Top Anime Link -->
                    <div class="section-header">
                        <div class="section-title-wrapper">
                            <h2 class="section-title">
                                <i class="fas fa-history me-2" style="color: var(--primary-purple);"></i>
                                Recently Updated
                            </h2>
                        </div>
                        <div class="section-nav-tabs">
                            <button class="filter-tab active" data-filter="series">Series</button>
                            <button class="filter-tab" data-filter="movie">Movie</button>
                            <button class="filter-tab" data-filter="ova">OVA</button>
                            <button class="filter-tab" data-filter="ona">ONA</button>
                            <button class="filter-tab" data-filter="donghua">Donghua</button>
                        </div>
                    </div>

                    <div class="anime-grid" id="recentlyUpdated">
                        <?php if (!empty($recentlyUpdated)): ?>
                            <?php foreach ($recentlyUpdated as $index => $anime): ?>
                                <a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="anime-card-link">
                                    <div class="anime-card" data-type="<?= strtolower(esc($anime['type'])) ?>">
                                        <div class="anime-poster">
                                            <img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop' ?>" 
                                                 alt="<?= esc($anime['title']) ?>" 
                                                 loading="lazy">
                                            <div class="anime-overlay">
                                                <div class="play-button">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            </div>
                                            <div class="anime-badge <?= getBadgeClass($anime['type']) ?>">
                                                <?= esc($anime['type']) ?>
                                            </div>
                                            <div class="episode-count">
                                                <?= !empty($anime['total_ep']) ? 'Ep ' . esc($anime['total_ep']) : 'New' ?>
                                            </div>
                                        </div>
                                        <div class="anime-info">
                                            <h3 class="anime-title" title="<?= esc($anime['title']) ?>">
                                                <?= esc(truncateTitle($anime['title'])) ?>
                                            </h3>
                                            <div class="anime-meta">
                                                <span class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <?= !empty($anime['ratings']) ? esc($anime['ratings']) : 'N/A' ?>
                                                </span>
                                                <span class="year"><?= esc($anime['status']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No anime found in the database.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="view-more-container">
                        <button class="view-more-btn">
                            <i class="fas fa-chevron-down me-2"></i>
                            View More
                        </button>
                    </div>

                    <!-- Action Section -->
                    <div class="section-header mt-5">
                        <h2 class="section-title">Action</h2>
                        <a href="#" class="view-all-link">more</a>
                    </div>
                    <div class="anime-grid">
                        <?php if (!empty($actionAnime)): ?>
                            <?php foreach ($actionAnime as $anime): ?>
                                <a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="anime-card-link">
                                    <div class="anime-card" data-type="action">
                                        <div class="anime-poster">
                                            <img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop' ?>" alt="<?= esc($anime['title']) ?>">
                                            <div class="anime-overlay">
                                                <div class="play-button">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            </div>
                                            <div class="anime-badge <?= getBadgeClass($anime['type']) ?>">
                                                <?= esc($anime['type']) ?>
                                            </div>
                                            <div class="episode-count"><?= !empty($anime['total_ep']) ? 'Ep ' . esc($anime['total_ep']) : 'New' ?></div>
                                        </div>
                                        <div class="anime-info">
                                            <h3 class="anime-title"><?= esc(truncateTitle($anime['title'])) ?></h3>
                                            <div class="anime-meta">
                                                <span class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <?= !empty($anime['ratings']) ? esc($anime['ratings']) : 'N/A' ?>
                                                </span>
                                                <span class="year"><?= esc($anime['status']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-3">
                                <p class="text-muted">No action anime found.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Movies Section -->
                    <div class="section-header mt-5">
                        <h2 class="section-title">Movies</h2>
                        <a href="#" class="view-all-link">more</a>
                    </div>
                    <div class="anime-grid">
                        <?php if (!empty($movieAnime)): ?>
                            <?php foreach ($movieAnime as $anime): ?>
                                <a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="anime-card-link">
                                    <div class="anime-card" data-type="movie">
                                        <div class="anime-poster">
                                            <img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop' ?>" alt="<?= esc($anime['title']) ?>">
                                            <div class="anime-overlay">
                                                <div class="play-button">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            </div>
                                            <div class="anime-badge <?= getBadgeClass($anime['type']) ?>"><?= esc($anime['type']) ?></div>
                                            <div class="episode-count"><?= !empty($anime['total_ep']) ? 'Ep ' . esc($anime['total_ep']) : 'Movie' ?></div>
                                        </div>
                                        <div class="anime-info">
                                            <h3 class="anime-title"><?= esc(truncateTitle($anime['title'])) ?></h3>
                                            <div class="anime-meta">
                                                <span class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <?= !empty($anime['ratings']) ? esc($anime['ratings']) : 'N/A' ?>
                                                </span>
                                                <span class="year"><?= esc($anime['status']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-3">
                                <p class="text-muted">No movies found.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Completed Anime Section -->
                    <div class="section-header mt-5">
                        <h2 class="section-title">Completed Anime</h2>
                        <a href="#" class="view-all-link">more</a>
                    </div>
                    <div class="anime-grid">
                        <?php if (!empty($completedAnime)): ?>
                            <?php foreach ($completedAnime as $anime): ?>
                                <a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="anime-card-link">
                                    <div class="anime-card" data-type="completed">
                                        <div class="anime-poster">
                                            <img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop' ?>" alt="<?= esc($anime['title']) ?>">
                                            <div class="anime-overlay">
                                                <div class="play-button">
                                                    <i class="fas fa-play"></i>
                                                </div>
                                            </div>
                                            <div class="anime-badge <?= getBadgeClass($anime['type']) ?>"><?= esc($anime['type']) ?></div>
                                            <div class="episode-count"><?= !empty($anime['total_ep']) ? 'Ep ' . esc($anime['total_ep']) : 'Complete' ?></div>
                                        </div>
                                        <div class="anime-info">
                                            <h3 class="anime-title"><?= esc(truncateTitle($anime['title'])) ?></h3>
                                            <div class="anime-meta">
                                                <span class="rating">
                                                    <i class="fas fa-star"></i>
                                                    <?= !empty($anime['ratings']) ? esc($anime['ratings']) : 'N/A' ?>
                                                </span>
                                                <span class="year"><?= esc($anime['status']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-3">
                                <p class="text-muted">No completed anime found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar Column - Top Anime -->
                <div class="col-lg-3">
                    <div class="sidebar">
                        <!-- Top Anime Section -->
                        <div class="sidebar-section">
                            <div class="sidebar-header">
                                <div class="sidebar-title-wrapper">
                                    <h3 class="sidebar-title">
                                        <i class="fas fa-trophy me-2" style="color: #FFD700;"></i>
                                        Top Anime
                                    </h3>
                                </div>
                                <div class="sidebar-nav">
                                    <button class="nav-tab active" data-period="today">Today</button>
                                    <button class="nav-tab" data-period="week">Week</button>
                                    <button class="nav-tab" data-period="month">Month</button>
                                </div>
                            </div>

                            <div class="sidebar-list" id="topAnimeList">
                                <?php if (!empty($trendingAnime)): ?>
                                    <?php foreach ($trendingAnime as $index => $anime): ?>
                                        <div class="sidebar-item">
                                            <div class="rank-number"><?= $index + 1 ?></div>
                                            <div class="item-thumbnail">
                                                <img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=50&h=70&fit=crop' ?>" alt="<?= esc($anime['title']) ?>">
                                            </div>
                                            <div class="item-details">
                                                <h5 class="item-title"><?= esc(strlen($anime['title']) > 25 ? substr($anime['title'], 0, 25) . '...' : $anime['title']) ?></h5>
                                                <div class="item-meta">
                                                    <span class="item-type"><?= esc($anime['type']) ?></span>
                                                    <span class="item-rating">★ <?= !empty($anime['ratings']) ? esc($anime['ratings']) : 'N/A' ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="sidebar-item">
                                        <div class="item-details">
                                            <p class="text-muted">No trending anime available.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Recommended Section -->
                        <div class="sidebar-section">
                            <div class="sidebar-header">
                                <h3 class="sidebar-title">
                                    <i class="fas fa-thumbs-up me-2"></i>
                                    Recommended
                                </h3>
                            </div>

                            <div class="sidebar-list" id="recommendedList">
                                <?php if (!empty($recommendedAnime)): ?>
                                    <?php foreach ($recommendedAnime as $anime): ?>
                                        <a href="<?= base_url('watch/' . createSlug($anime['title'])) ?>" class="sidebar-item recommended" style="text-decoration: none; color: inherit;">
                                            <div class="item-thumbnail">
                                                <img src="<?= !empty($anime['backgroundImage']) ? esc($anime['backgroundImage']) : 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=50&h=70&fit=crop' ?>" alt="<?= esc($anime['title']) ?>">
                                            </div>
                                            <div class="item-details">
                                                <h5 class="item-title"><?= esc(strlen($anime['title']) > 25 ? substr($anime['title'], 0, 25) . '...' : $anime['title']) ?></h5>
                                                <div class="item-meta">
                                                    <span class="item-type"><?= !empty($anime['total_ep']) ? 'Ep ' . esc($anime['total_ep']) : esc($anime['type']) ?></span>
                                                    <span class="item-genre">• <?= !empty($anime['genres']) ? esc(explode(',', $anime['genres'])[0]) : 'Unknown' ?> • <?= esc($anime['type']) ?></span>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="sidebar-item recommended">
                                        <div class="item-details">
                                            <p class="text-muted">No recommendations available.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="footer-brand">
                        <a href="<?= base_url('/') ?>" class="footer-logo" style="text-decoration: none; color: inherit;">
                            <div class="logo-icon">
                                <i class="fas fa-play"></i>
                            </div>
                            <span>rio<span style="color: var(--primary-purple);">wave</span></span>
                        </a>
                        <p class="footer-description">
                            Copyright riowave. All Rights Reserved.<br>
                            You can view our <a href="#">FAQ</a> and our <a href="#">terms of service</a>. All content are provided by non-affiliated third parties.
                        </p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h4 class="footer-title">LINKS</h4>
                        <ul class="footer-links">
                            <li><a href="#">A-Z List</a></li>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Bookmark</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="footer-section">
                        <h4 class="footer-title">HELP</h4>
                        <ul class="footer-links">
                            <li><a href="#">Contact</a></li>
                            <li><a href="#">Request</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/homepage.js') ?>"></script>
</body>
</html>