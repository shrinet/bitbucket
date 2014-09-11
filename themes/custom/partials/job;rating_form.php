<? if ($rating): ?>
	<h4><?=__('Review of %s', $opp_user_name)?></h4>
	<blockquote>
		<div class="rating star-rating"><i class="rating-<?=$rating->rating*10?>"></i></div>
		<p>"<?=Phpr_String::show_more_link($rating->comment, 125, __('Show more', true))?>"</p>
		<small><cite><?=$rating->user_from->username?></cite>, <?=$rating->user_from->location_string?></small>
	</blockquote>
<? else: ?>
	<h4><?=__('Submit a review about %s', $opp_user_name)?></h4>

	<div class="control-group">
		<label for="rating_comment" class="control-label"><?=__('Write something about %s', $opp_user_name)?></label>
		<div class="controls">
			<?=form_textarea('Rating[comment]', '', 'id="rating_comment" class="span12"')?>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span6">
			<div class="control-group">
				<label for="rating_rating" class="control-label"><?=__('Select a rating')?></label>
				<div class="controls">
					<div class="rating-selector" id="rating_rating">
						<?=form_dropdown('Rating[rating]', array(0,1,2,3,4,5))?>
					</div>
				</div>
			</div>
		</div>
		<div class="span6 align-right">
			<button type="submit" name="submit" class="btn btn-primary btn-large"><?= __('Submit this rating') ?></button>
		</div>
	</div>
	<script type="text/javascript"> jQuery(document).ready(function($) { $('#rating_rating').starRating(); }); </script>

<script>

	Page.jobRatingFormFields = $.phpr.form().defineFields(function(){
	   this.defineField('Rating[comment]').required("<?=__('Please write a short review comment')?>");
	});

</script>
<? endif ?>