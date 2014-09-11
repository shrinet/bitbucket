<div class="row-fluid">
	<div class="span3 hide-for-small">
		<h4><?=__('Profile summaries')?></h4>

		<ul class="nav nav-tabs nav-stacked">
			<? foreach ($profiles as $profile): ?>
				<li><a href="<?=root_url('provide/manage/'.$profile->id)?>"><?=$profile->role_name?></a></li>
			<? endforeach ?>
		</ul>

		<a href="<?=root_url('provide/create')?>" class="btn btn-primary btn-block">
			<i class="icon-plus"></i>
			<?=__('Create Skill Profile')?>
		</a>

		<ul class="quick-links">
			<li><a href="<?=root_url('account/notifications')?>"><?=__('Notification preferences')?></a></li>
			<li><a href="<?=root_url('account/work_history')?>"><?=__('Work history')?></a></li>
		</ul>
	</div>
	<div class="span9">
		<div class="row-fluid">
			<div class="span8">

				<div class="provide-panel">
					<? foreach ($profiles as $index=>$profile): ?>
						<? if ($index != 0): ?><hr /><? endif ?>
						<?=$this->display_partial('dash:provide_panel', array('provider_profile'=>$profile))?>
					<? endforeach?>
				</div>

			</div>

			<div class="span4">
				<div id="p_dash_booking_panel">
					<?=$this->display_partial('dash:booking_panel')?>
				</div>

				<div id="p_dash_message_panel">
					<?=$this->display_partial('dash:message_panel')?>
				</div>
			</div>

		</div>
	</div>
</div>
