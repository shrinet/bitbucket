<?
	$top_providers = Service_Provider::create()->find_top_providers($this->user)->limit(3)->find_all();
?>
<? if ($top_providers->count > 0): ?>
	<h5><?=__('View top profiles')?></h5>
	<? foreach ($top_providers as $top_provider): ?>
		<p>
			<a href="<?=$top_provider->get_url('profile')?>"><?=$top_provider->business_name?></a>
			<br /><?=$top_provider->role_name?>
		</p>
	<? endforeach ?>
<? endif ?>