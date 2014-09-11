<div class="row-fluid">
	<div class="span8">
		<div class="page-header">
			<h1><?=$this->page->title_name?></h1>
			<h4 class="subheader"><?=$this->page->description?></h4>
		</div>

		<ul class="post_list">
			<? foreach ($post_list as $post): ?>
			<li>
				<h3><a href="<?=root_url('blog/post/'.$post->url_title) ?>"><?=h($post->title) ?></a></h3>
				<p class="light">
					<?=__('Published by %s on %s.', array(
						h($post->author_first_name.' '.substr($post->author_last_name, 0, 1).'.'),
						$post->published_at->format('%F')
					))?>
					<?=__('Comments')?>: <?=$post->approved_comment_num ?>
				</p>
				<p><?=h($post->description) ?> <a href="<?=root_url('blog/post/'.$post->url_title) ?>"><?=__('Show more', true)?></a></p>
			</li>
		<? endforeach ?>
		</ul>

		<div class="view_controls">
			<? $this->display_partial('site:pagination', array('pagination'=>$pagination, 'base_url'=>root_url('blog'))); ?>
		</div>

	</div>
	<div class="span4">
		<div id="blog_sidebar">
			<?=$this->display_partial('blog:sidebar')?>
		</div>
	</div>
</div>
