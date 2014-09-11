<?
	$ratings = (isset($ratings)) ? $ratings : null;
	$pagination = (isset($pagination)) ? $pagination : false;
?>
<? if ($ratings): ?>
	<? 
		$ratings = $ratings->limit(2)->find_all();
	?>
	<? foreach ($ratings as $rating): ?>
		<? if ($rating instanceof Service_Rating): ?>
			<div class="profile-rating-small">
				<p class="star-rating single name"><?=$rating->user_from->name?><i class="pull-right rating-<?=$rating->rating*10?>">&nbsp;</i></p>
				<?=Phpr_String::show_more_link($rating->comment, 125, __('Show more', true))?>
				
			</div>
		<? elseif ($rating instanceof Service_Testimonial): ?>
			<blockquote>
				"<?=$rating->comment?>"
				<small><?=$rating->name?>, <?=$rating->location?></small>
			</blockquote>
		<? endif ?>
	<? endforeach ?>
	
<? endif ?>