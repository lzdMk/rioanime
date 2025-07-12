-- Backup SQL for rioanime database

-- Table: anime_data
CREATE TABLE `anime_data` (
  `anime_id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `language` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `total_ep` int(11) DEFAULT NULL,
  `ratings` text DEFAULT NULL,
  `genres` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `studios` text DEFAULT NULL,
  `urls` text DEFAULT NULL,
  `backgroundImage` text DEFAULT NULL,
  `synopsis` text DEFAULT NULL,
  PRIMARY KEY (`anime_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample insert for anime_data
INSERT INTO `anime_data` (`anime_id`, `title`, `language`, `type`, `total_ep`, `ratings`, `genres`, `status`, `studios`, `urls`, `backgroundImage`, `synopsis`) VALUES
(100001, 'Sample Anime', 'Japanese', 'TV', 12, '8.5', 'Action, Adventure', 'Completed', 'Sample Studio', 'https://example.com', 'https://example.com/bg.jpg', 'This is a sample synopsis.');

-- Table: anime_views
CREATE TABLE `anime_views` (
  `anime_id` int(11) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `user_ip` varchar(45) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `viewed_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`anime_id`,`user_ip`),
  KEY `anime_id_user_ip_viewed_at` (`anime_id`,`user_ip`,`views`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample insert for anime_views
INSERT INTO `anime_views` (`anime_id`, `title`, `user_ip`, `views`, `viewed_at`) VALUES
(100001, 'Sample Anime', '127.0.0.1', 1, NOW());

-- Table: user_accounts
CREATE TABLE `user_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `display_name` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` enum('viewer','moderator','admin') NOT NULL DEFAULT 'viewer',
  `followed_anime` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample insert for user_accounts
INSERT INTO `user_accounts` (`username`, `display_name`, `email`, `password`, `type`, `followed_anime`, `created_at`) VALUES
('sampleuser', 'Sample User', 'sample@example.com', '$2y$10$wH6QJQwQwQwQwQwQwQwQwOeQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw', 'viewer', '', NOW());
