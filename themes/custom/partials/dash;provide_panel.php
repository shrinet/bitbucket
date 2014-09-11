<?
	$provider = (isset($provider_profile)) ? $provider_profile : null;
?>
<? if ($provider): ?>
	<h4><?=$provider->role_name?> <?=__('profile', true)?></h4>
	<div class="row-fluid">
		<div class="span3">
			<div class="header"><?=__('Earned')?></div>
			<div class="large align-center"><?=format_currency($provider->stat_earned)?></div>
		</div>
		<div class="span5">
			<div class="header"><?=__('Jobs')?></div>
			<ul class="block-grid grid-span3">
				<li class="align-center"><div class="large"><?=$provider->stat_offers?></div><div><?=__('offers')?></div></li>
				<li class="align-center"><div class="large"><?=$provider->stat_quotes?></div><div><?=__('quotes')?></div></li>
				<li class="align-center"><div class="large"><?=$provider->stat_wins?></div><div><?=__('wins')?></div></li>
			</ul>
		</div>
		<div class="span4">
			<div class="header"><?=__('Rating')?></div>
			<p class="star-rating align-center">
				<i class="rating-<?=$provider->rating*10?>">&nbsp;</i>
				<br />
				<? if ($provider->rating_num > 0): ?>
					(<?=__('%s reviews', $provider->rating_num)?>)
				<? else: ?>
					(<?=__('No ratings yet')?>)
				<? endif?>
			</p>
		
		</div>
	</div>
	<div class="align-right">
		<a href="<?=root_url('provide/manage/'.$provider->id)?>" class="btn btn-small"><?=__('View/Edit Profile')?></a>
	</div>
<? endif?>