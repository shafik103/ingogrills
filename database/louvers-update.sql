-- Rename "Louvres" to "Louvers" in the database (category, product text, About page).
-- Run once on each database (local and live). Table prefix wp_.

-- Category name and URL slug (/product-category/louvres/ -> /product-category/louvers/)
UPDATE wp_terms SET name = 'Louvers', slug = 'louvers' WHERE slug = 'louvres';

-- Text in products and pages
UPDATE wp_posts SET
  post_title   = REPLACE(REPLACE(post_title,   'Louvres', 'Louvers'), 'louvres', 'louvers'),
  post_excerpt = REPLACE(REPLACE(post_excerpt, 'Louvres', 'Louvers'), 'louvres', 'louvers'),
  post_content = REPLACE(REPLACE(post_content, 'Louvres', 'Louvers'), 'louvres', 'louvers')
WHERE post_type IN ('product', 'page', 'revision')
  AND (post_title LIKE '%ouvres%' OR post_excerpt LIKE '%ouvres%' OR post_content LIKE '%ouvres%');

-- Check: should return 0 rows
SELECT ID, post_type, post_title FROM wp_posts
WHERE post_type IN ('product','page','revision') AND (post_title LIKE '%ouvres%' OR post_excerpt LIKE '%ouvres%' OR post_content LIKE '%ouvres%');
SELECT term_id, name, slug FROM wp_terms WHERE slug LIKE 'louv%';
