-- Change the site address from local to live.
-- Run in phpMyAdmin (SQL tab) on the LIVE database, after importing indogrills.sql.
-- Take a backup/export first. If your table prefix is not wp_, change it below.
--
-- Old address: http://localhost/indogrills
-- New address: https://indogrills.com
--
-- Safe for this site: none of the stored values containing the old address are
-- serialized (checked), so a plain REPLACE cannot corrupt data.

UPDATE wp_options  SET option_value = REPLACE(option_value, 'http://localhost/indogrills', 'https://indogrills.com') WHERE option_value LIKE '%http://localhost/indogrills%';
UPDATE wp_posts    SET post_content = REPLACE(post_content, 'http://localhost/indogrills', 'https://indogrills.com') WHERE post_content LIKE '%http://localhost/indogrills%';
UPDATE wp_posts    SET post_excerpt = REPLACE(post_excerpt, 'http://localhost/indogrills', 'https://indogrills.com') WHERE post_excerpt LIKE '%http://localhost/indogrills%';
UPDATE wp_posts    SET guid         = REPLACE(guid,         'http://localhost/indogrills', 'https://indogrills.com') WHERE guid         LIKE '%http://localhost/indogrills%';
UPDATE wp_postmeta SET meta_value   = REPLACE(meta_value,   'http://localhost/indogrills', 'https://indogrills.com') WHERE meta_value   LIKE '%http://localhost/indogrills%' AND meta_value NOT REGEXP '^[aOs]:[0-9]+:';
UPDATE wp_users    SET user_url     = REPLACE(user_url,     'http://localhost/indogrills', 'https://indogrills.com') WHERE user_url     LIKE '%http://localhost/indogrills%';

-- Check: all three numbers should be 0
SELECT
  (SELECT COUNT(*) FROM wp_options  WHERE option_value LIKE '%localhost%') AS options_left,
  (SELECT COUNT(*) FROM wp_posts    WHERE post_content LIKE '%localhost%' OR guid LIKE '%localhost%') AS posts_left,
  (SELECT COUNT(*) FROM wp_postmeta WHERE meta_value   LIKE '%localhost%') AS postmeta_left;

-- Confirm the new address
SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl','home');
