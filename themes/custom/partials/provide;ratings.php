<?
	$ratings = (isset($ratings)) ? $ratings : null;
	$pagination = (isset($pagination)) ? $pagination : false;
?>
<? if ($ratings): ?>
	<? 
		$ratings = $ratings->find_all();
	?>
	<? foreach ($ratings as $rating): ?>
		<? if ($rating instanceof Service_Rating): ?>
			<div>
				<p class="star-rating single"><?=$rating->user_from->name?><i class="pull-right rating-<?=$rating->rating*10?>">&nbsp;</i></p>
				"<?=Phpr_String::show_more_link($rating->comment, 125, __('Show more', true))?>"
				
			</div>
		<? elseif ($rating instanceof Service_Testimonial): ?>
			<blockquote>
				"<?=$rating->comment?>"
				<small><?=$rating->name?>, <?=$rating->location?></small>
			</blockquote>
		<? endif ?>
	<? endforeach ?>
	<? if ($pagination): ?>
		<? $this->display_partial('site:pagination', array('pagination'=>$pagination, 'base_url'=>$base_url)); ?>
	<? endif?>
<? endif ?>