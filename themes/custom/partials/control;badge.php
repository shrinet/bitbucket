<?
	$badge_mode = (isset($badge_mode)) ? $badge_mode : 'normal';
?>
<div class="row-fluid <?=$badge_mode?>">
<? if ($badge_mode == 'detailed'): ?>
	<div class="span4 mobile-span3">
		<div class="avatar"><img src="<?=Bluebell_Provider::avatar($provider)?>" alt="<?=$provider->business_name?>" class="img-circle" /></div>          
	</div>
	<div class="span8 mobile-span9">
		<div class="name"><?=$provider->business_name?></div>
		<div class="skils"><?=$provider->role_name?></div>
		<div class="location"><?=$provider->location_string?></div>
		<div class="rating star-rating"><i class="rating-<?=$provider->rating*10?>"></i></div>
		<div class="link"><a href="<?=$provider->get_url('profile')?>" class="btn btn-small"><?=__('View Profile')?></a></div>
	</div>
<? else: ?>
	<div class="span4 mobile-span3">
		<div class="avatar"><img src="<?=Bluebell_Provider::avatar($provider)?>" alt="<?=$provider->business_name?>" /></div>
	</div>
	<div class="span8 mobile-span9">
		<div class="skills"><?=$provider->role_name?></div>
		<div class="location"><?=$provider->location_string?></div>
	</div>
<? endif ?>
</div>