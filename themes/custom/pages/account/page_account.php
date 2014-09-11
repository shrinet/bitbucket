<?
	$selected_tab = $this->request_param(0, 'account');
	if ($selected_tab == 'offers' || $selected_tab == 'requests')
		$selected_tab = 'work_history';
?>
<div class="row-fluid">
	<div class="span9">

		<div class="tabbable">

			<ul class="nav nav-tabs">
				<li class="<?=$selected_tab=='account'?'active':''?>"><a href="#account" data-toggle="tab"><?=__('Account')?></a></li>
				<li class="<?=$selected_tab=='finances'?'active':''?>"><a href="#finances" data-toggle="tab"><?=__('Finances')?></a></li>
				<? if ($this->user->is_provider): ?>
					<li class="<?=$selected_tab=='skill_profiles'?'active':''?>"><a href="#skill_profiles" data-toggle="tab"><?=__('Skill Profiles')?></a></li>
					<li class="<?=$selected_tab=='withdraw_profiles'?'active':''?>"><a href="#withdraw_profiles" data-toggle="tab"><?=__('Withdraw Profiles')?></a></li>
				<? endif ?>
				<li class="<?=$selected_tab=='notifications'?'active':''?>"><a href="#notifications" data-toggle="tab"><?=__('Notifications')?></a></li>
				<li class="<?=$selected_tab=='work_history'?'active':''?>"><a href="#work_history" data-toggle="tab"><?=__('Work History')?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane <?=$selected_tab=='account'?'active':''?> account" id="account">
					<div class="content"><?=$this->display_partial('account:details')?></div>
				</div>

				<div class="tab-pane <?=$selected_tab=='finances'?'active':''?> finances" id="finances">
					<div class="content"><?=$this->display_partial('account:finances')?></div>
				</div>

				<? if ($this->user->is_provider): ?>
					<div class="tab-pane <?=$selected_tab=='skill_profiles'?'active':''?> profiles" id="skill_profiles">
						<div class="content"><?=$this->display_partial('account:skill_profiles')?></div>
					</div>
					<div class="tab-pane <?=$selected_tab=='withdraw_profiles'?'active':''?> profiles" id="withdraw_profiles">
						<div class="content"><?=$this->display_partial('account:withdraw_profiles')?></div>
					</div>
				<? endif ?>

				<div class="tab-pane <?=$selected_tab=='notifications'?'active':''?> notifications" id="notifications">
					<div class="content"><?=$this->display_partial('account:notifications')?></div>
				</div>

				<div class="tab-pane <?=$selected_tab=='work_history'?'active':''?> work-history" id="work_history">
					<div class="content"><?=$this->display_partial('account:work_history')?></div>
				</div>
			</div>
		</div>

	</div>
</div>