<? if ($this->user->is_provider): ?>
<div class="box">
	<div class="box-header">
		<h6><?=__('Welcome to %s', c('site_name'))?></h6>
	</div>
	<div class="box-content">
		
		<div class="row-fluid">
			<div class="span5">
				<div class="row-fluid">
					<div class="span5 mobile-span2">
						<div class="avatar">
							<img src="<?=Bluebell_Provider::avatar($latest_profile)?>" alt="<?=$latest_profile->business_name?>" class="img-polaroid" />
						</div>
					</div>
					<div class="span7 mobile-span2">
						<div class="business_name">
							<p class="name"><a href="<?=root_url('provide/manage/'.$latest_profile->id)?>"><?=$latest_profile->business_name?></a></p>
							<p class="title"><strong><?=$latest_profile->role_name?></strong></p>
							<p class="location"><?=$latest_profile->location_string?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="span7">
				<ul class="block-grid grid-span3 welcome_icons">
					<li>
						<a href="<?= root_url('provide/profiles') ?>">
							<i class="fa fa-edit"></i> 
							<span class="link-button"><?=__('Profile')?></a>
						</a>
					</li>
					<li>
						<a href="<?= root_url('account') ?>">
							<i class="fa fa-user"></i> 
							<span class="link-button"><?=__('Account')?></span>
						</a>
					</li>
					<li>
						<a href="<?= root_url('provide/how-it-works') ?>">
							<i class="fa fa-book"></i> 
							<span class="link-button"><?=__('Learn More')?></span>
						</a>
					</li>
				</ul>
			</div>
		</div>

	</div>
</div>
<? endif ?>