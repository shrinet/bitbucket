<? foreach ($profiles as $profile): ?>
<div class="profile">
	<div class="row-fluid">
		<div class="span6 columns mobile-two">
			<a href="<?=root_url('provide/manage/'.$profile->id)?>" class="role_name"><?=$profile->role_name?></a>
		</div>
		<div class="span6 columns mobile-two align_right">
			<div class="status active">Active</div>
		</div>
	</div>
</div>
<? endforeach ?>