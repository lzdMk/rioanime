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