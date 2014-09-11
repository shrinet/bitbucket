<? if (!isset($comment_success) || !$comment_success): ?>
	<h4><?=__('Comment on this post')?></h4>
	<?=form_open(array('onsubmit'=>"return $(this).phpr().post('blog:on_create_comment').update('#comment_form', 'blog:comment_form').send()")) ?>
		<div class="blog_comment_form">

			<div class="row-fluid">
				<div class="row-fluid">
					<div class="span6 mobile-span2">
						<div class="control-group">
							<label for="author_name" class="control-label"><?=__('Name', true)?></label>
							<div class="controls">
								<input id="author_name" type="text" name="author_name" class="span12" /><br/>
							</div>
						</div>
					</div>
					<div class="span6 mobile-span2">
						<div class="control-group">
							<label for="author_email" class="control-label"><?=__('Email Address', true)?></label>
							<div class="controls">
								<input type="text" id="author_email" name="author_email" class="span12" /><br/>
							</div>
						</div>
					</div>
				</div>

				<div class="control-group">
					<label for="author_url" class="control-label"><?=__('Website URL')?></label>
					<div class="controls">
						<input type="text" id="author_url" name="author_url" class="span12" /><br/>
					</div>
				</div>

				<div class="control-group">
					<label for="comment_content" class="control-label"><?=__('Comment')?></label>
					<div class="controls">
						<textarea id="comment_content" name="content" class="span12"></textarea>
					</div>
				</div>
			</div>
			<div class="form-actions">
				<?=form_submit('submit', __('Submit Comment'), 'class="btn btn-primary"')?>
			</div>
		</div>

	<?=form_close()?>
<? else: ?>
	<div class="alert-box success">
		<?=__('Your comment has been posted. Thank you!')?>
		<a href="" class="close">&times;</a>
	</div>
<? endif ?>