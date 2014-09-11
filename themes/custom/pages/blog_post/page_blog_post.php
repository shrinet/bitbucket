<? if ($post): ?>

	<div class="page-header">
		<h1><?=$this->page->title_name?></h1>
		<h4 class="subheader"><?=$this->page->description?></h4>
	</div>

	<div class="blog-post-content">
		<?=$post->content ?>
	</div>

	<p class="blog-post-intro">
		<?
			$category_string = '';
			foreach ($post->categories as $index=>$category)
				$category_string .= '<a href="'.root_url('blog/category/'.$category->url_name).'">'.h($category->name).'</a>'.($index < $post->categories->count-1 ? ', ' : '.');
		?>            

		<?=__('Published by %s on %s in %s', array(
			h($post->author_first_name.' '.substr($post->author_last_name, 0, 1).'.'),                 
			$post->published_at->format('%F'),
			$category_string
		))?>

		<?=__('Comments')?>: <?=$post->approved_comment_num ?>
	</p>

	<h4><?=__('Comments')?></h4>
	
	<? if (!$post->approved_comment_num): ?>
		<p><?=__('No comments have been posted yet')?></p>
	<? else: ?>
		<ul class="blog-comment-list">
		<? foreach ($post->approved_comments as $comment):
			$site_url_specified = strlen($comment->author_url);
		?>
			<li class="<?=$comment->is_owner_comment ? 'owner_comment' : null  ?>">
				<p>
					<?
						$author_string = '';
						if ($site_url_specified)
							$author_string .= '<a href="'.$comment->get_url_formatted().'">';

						$author_string .= h($comment->author_name);

						if ($site_url_specified)
							$author_string .= '</a>';
					?>
					<?=__('Posted by %s on %s', array(
						$author_string, 
						$comment->created_at->format('%F')
					))?>
				</p>
				<p><?=nl2br(h($comment->content)) ?></p>
			</li>
		<? endforeach ?>
		</ul>
	<? endif ?>
	
	<? if ($post->comments_allowed): ?>
		<div id="comment_form"><? $this->display_partial('blog:comment_form') ?></div>
	<? else: ?>
		<p class="comments_closed"><?=__('Comments are not permitted for this post')?></p>
	<? endif ?>

<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that post could not be found')))?>
<? endif ?>