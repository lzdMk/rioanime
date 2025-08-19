-- Manual Migration SQL for Published Anime Feature
-- Run this if the migration script doesn't work

-- Add the new columns to anime_data table
ALTER TABLE `anime_data` 
ADD COLUMN `published` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = published, 0 = unpublished/draft',
ADD COLUMN `published_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When the anime was published',
ADD COLUMN `unpublished_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When the anime was unpublished';

-- Update existing records to be published by default
UPDATE `anime_data` SET `published` = 1, `published_at` = NOW() WHERE `published` IS NULL OR `published` = 0;

-- Verify the changes
SELECT COUNT(*) as total_anime, 
       SUM(published) as published_anime, 
       COUNT(*) - SUM(published) as unpublished_anime 
FROM anime_data;

DESCRIBE anime_data;
