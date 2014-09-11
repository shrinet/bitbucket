TRUNCATE TABLE `blog_categories`;
INSERT INTO `blog_categories` (`id`, `name`, `url_name`, `code`, `description`, `created_at`, `created_user_id`) VALUES
(1, 'Uncategorized', 'uncategorized', NULL, now(), NULL, 1);


TRUNCATE TABLE `blog_posts`;
INSERT INTO `blog_posts` (`id`, `created_at`, `updated_at`, `created_user_id`, `title`, `description`, `content`, `published_at`, `is_published`, `category_id`, `url_title`, `comments_allowed`) VALUES
(1, now(), NULL, 1, 'First blog post', 'This if your first blog post for Your Site. You can edit this page by selecting CMS > Pages in the administration back-end. Enjoy!', '<p>This if your first <strong>blog post</strong> for Your Site.</p>\n<p>You can edit this page by selecting <strong>CMS &gt; Pages</strong> in the administration back-end.</p>\n<p><em>Enjoy!</em></p>', now(), 1, NULL, 'first-blog-post', 1);


TRUNCATE TABLE `blog_comments`;
INSERT INTO `blog_comments` (`id`, `created_at`, `author_name`, `author_url`, `author_ip`, `post_id`, `content`, `author_email`, `status_id`, `content_html`, `is_owner_comment`) VALUES
(1, now(), 'Joe', NULL, '127.0.0.1', 1, 'Great demo! I really like what you have done with the site!', 'joe@bloggs.com', 2, '<p>I really like what you have done with the site!</p>', NULL);
