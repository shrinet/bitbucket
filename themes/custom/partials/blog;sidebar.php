<h5><?=__('Blog categories')?></h5>
<ul class="category_list">
	<?
		$categories = Blog_Category::create()->find_all();
	?>
	<? foreach ($categories as $category): ?>
		<li>
			<a href="<?=root_url('blog/category/'.$category->url_name) ?>"><?=h($category->name) ?></a>
		</li>
	<? endforeach ?>
</ul>

<div class="panel">
	<h5><?=__('RSS channel')?></h5>
	<p><?=__('Subscribe to our %s to stay updated with our latest blog news.', '<a href="'.root_url('blog/rss').'">'.__('RSS feed').'</a>')?></p>
</div>