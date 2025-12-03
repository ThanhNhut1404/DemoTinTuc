-- Migration: add related_tags and seo_keywords to the_tag table
-- Run this SQL in your database (phpMyAdmin or mysql client)

ALTER TABLE `the_tag`
  ADD COLUMN `related_tags` TEXT NULL DEFAULT NULL COMMENT 'Comma-separated related internal tags',
  ADD COLUMN `seo_keywords` TEXT NULL DEFAULT NULL COMMENT 'Comma-separated SEO keywords for the tag';

-- After running, existing tag rows will have NULL in these columns.
