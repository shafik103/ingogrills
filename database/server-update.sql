-- ONE-STEP UPDATE for a database that is already imported on the live server.
-- Use this ONLY if you imported database/indogrills.sql (the local dump).
-- If you import database/indogrills-live.sql instead, you do NOT need this file.
-- Run in phpMyAdmin > SQL tab. Back up (Export) first. Table prefix: wp_

-- 1) Site address: local -> live
UPDATE wp_options  SET option_value = REPLACE(option_value, 'http://localhost/indogrills', 'https://indogrills.com') WHERE option_value LIKE '%http://localhost/indogrills%';
UPDATE wp_posts    SET post_content = REPLACE(post_content, 'http://localhost/indogrills', 'https://indogrills.com') WHERE post_content LIKE '%http://localhost/indogrills%';
UPDATE wp_posts    SET post_excerpt = REPLACE(post_excerpt, 'http://localhost/indogrills', 'https://indogrills.com') WHERE post_excerpt LIKE '%http://localhost/indogrills%';
UPDATE wp_posts    SET guid         = REPLACE(guid,         'http://localhost/indogrills', 'https://indogrills.com') WHERE guid         LIKE '%http://localhost/indogrills%';
UPDATE wp_postmeta SET meta_value   = REPLACE(meta_value,   'http://localhost/indogrills', 'https://indogrills.com') WHERE meta_value   LIKE '%http://localhost/indogrills%' AND meta_value NOT REGEXP '^[aOs]:[0-9]+:';
UPDATE wp_users    SET user_url     = REPLACE(user_url,     'http://localhost/indogrills', 'https://indogrills.com') WHERE user_url     LIKE '%http://localhost/indogrills%';

-- 2) Louvres -> Louvers (category name + URL slug, product and page text)
UPDATE wp_terms SET name = 'Louvers', slug = 'louvers' WHERE slug = 'louvres';
UPDATE wp_posts SET
  post_title   = REPLACE(REPLACE(post_title,   'Louvres', 'Louvers'), 'louvres', 'louvers'),
  post_excerpt = REPLACE(REPLACE(post_excerpt, 'Louvres', 'Louvers'), 'louvres', 'louvers'),
  post_content = REPLACE(REPLACE(post_content, 'Louvres', 'Louvers'), 'louvres', 'louvers')
WHERE post_type IN ('product', 'page', 'revision')
  AND (post_title LIKE '%ouvres%' OR post_excerpt LIKE '%ouvres%' OR post_content LIKE '%ouvres%');

-- 3) Checks. Expect: localhost_left = 0, louvres_left = 0, and the new address below.
SELECT
  (SELECT COUNT(*) FROM wp_options  WHERE option_value LIKE '%localhost%')                                   AS options_localhost,
  (SELECT COUNT(*) FROM wp_posts    WHERE post_content LIKE '%localhost%' OR guid LIKE '%localhost%')        AS posts_localhost,
  (SELECT COUNT(*) FROM wp_posts    WHERE post_type IN ('product','page','revision') AND (post_title LIKE '%ouvres%' OR post_excerpt LIKE '%ouvres%' OR post_content LIKE '%ouvres%')) AS louvres_left;
SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl','home');
SELECT term_id, name, slug FROM wp_terms WHERE slug LIKE 'louv%';
