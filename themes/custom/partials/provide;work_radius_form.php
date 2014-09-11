<?
	$radius_max = 100; // Maximum radius for work area
	$radius_unit = Location_Config::create()->default_unit; // Miles (mi) or Kilometers (km)
?>
<?=form_hidden('work_radius_max', $radius_max, 'id="work_radius_max"')?>
<?=form_hidden('work_radius_unit', $radius_unit, 'id="work_radius_unit"')?>
<div class="row-fluid disabled" id="work_radius">
	<div class="span8">
		<div class="">
			<div id="work_radius_radius"><span><?= form_value($provider, 'service_radius', 25) ?></span> <?=$radius_unit?></div>
			<?=form_label(__('How far are you willing to travel?'))?>
			<div class="radius_slider">
				<div id="work_radius_slider"></div>
				<ul id="work_radius_slider_legend">
					<? for ($x = 0; $x < 11; $x++):  // Do eleven times ?>
						<? if ($x==0): ?>
							<li class="first">1</li>
						<? elseif ($x==10): ?>
							<li class="last"><?=$radius_max?></li>
						<? else: ?>
							<li><?=round(($radius_max/10)*($x))?></li>
						<? endif ?>
					<? endfor ?>
				</ul>
			</div>
			<div id="work_radius_map"></div>
		</div>
	</div>
	<div class="span4">
		<div id="work_radius_areas">
			<h5><?=__('Your work area includes...')?></h5>
			<ul id="work_radius_area_list" data-empty-text="<?=__('(No nearby areas)')?>">
				<li><?=__('(No nearby areas)')?></li>
			</ul>
		</div>
	</div>
</div>
<div id="work_radius_disabled" class="well">
	<?=__('Please enter the address for your business')?>
</div>
<input type="text" name="work_radius_address" value="" id="work_radius_address" style="display:none" />
<input type="text" name="Provider[service_codes]" value="<?= form_value($provider, 'service_codes') ?>" id="provider_service_codes" style="display:none" />
<input type="text" name="Provider[service_radius]" value="<?= form_value($provider, 'service_radius', 25) ?>" id="provider_service_radius" style="display:none" />
