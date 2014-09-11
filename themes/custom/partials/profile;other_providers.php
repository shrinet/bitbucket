<div id="map_other_providers" class="google_map hide-for-small">
</div>
<div id="other_providers">
	<ul>
		<? foreach ($other_providers as $o_provider): ?>

			<li data-id="info_provider<?=$o_provider->id?>" data-latlng="<?=$o_provider->latitude?>,<?=$o_provider->longitude?>" data-address="<?=$o_provider->address_string?>">
				<div class="avatar"><img src="<?=Bluebell_Provider::avatar($o_provider)?>" alt="<?=$o_provider->business_name?>" /></div>
				<div class="name"><?=$o_provider->business_name?></div>
				<div class="rating star-rating">
					<i class="rating-<?=$o_provider->rating*10?>"></i>
				</div>
			</li>
		<? endforeach ?>
	</ul>
</div>
<div style="display:none">
<? foreach ($other_providers as $o_provider): ?>
	<div id="location_info_provider<?=$o_provider->id?>">
		<div class="page_profile_map_bubble">

			<div class="box">
				<div class="box-header">
					<h3><?=$o_provider->business_name?> <small><?=$o_provider->role_name?></small></h3>
				</div>
				<div class="box-content">
					<div class="row-fluid">
						<div class="span4 align-center">
							<img src="<?=Bluebell_Provider::avatar($o_provider)?>" alt="<?=$o_provider->business_name?>" class="rounded" />                            
							<p><a href="<?=$o_provider->get_url('profile')?>" class="btn"><?=__('View Profile')?></a></p>
						</div>
						<div class="span6">
							<p><?=Phpr_Html::limit_characters($o_provider->description,100)?></p>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<div class="row-fluid">
						<div class="span5">
							<div class="rating star-rating">
								<i class="rating-<?=$o_provider->rating*10?>"></i>
							</div>
						</div>
						<div class="span5 align-right">
							<h6><?=$o_provider->location_string?></h6>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
<? endforeach ?>
</div>