<?=Blog_Post::get_rss(
	__('Blog'),
	__('News and updates'),
	root_url('rss', true),
	root_url('blog/post', true),
	root_url('blog/category', true),
	root_url('blog', true),
	20
)?>