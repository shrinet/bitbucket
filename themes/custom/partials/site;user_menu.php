<? if ($this->user) { ?>
	<ul class="nav nav-pills pull-right user">
		<li class="userLoggedIn">
			<span><?=$this->user->username?></span>
			<ul>
				<li><a href="<?=root_url('dashboard')?>"><?=__('Dashboard', true)?></a></li>
				<li><a href="<?=root_url('request')?>"><?=__('Request Service', true)?></a></li>
				<li><a href="<?=root_url('provide')?>"><?=__('Provide Services', true)?></a></li>
				<? if (!$this->user->is_requestor): ?><? endif ?>
				<? if (!$this->user->is_provider): ?><? endif ?>
				<li><a href="<?=root_url('account')?>"><?=__('Account Settings', true)?></a></li>
				<li><a href="<?=root_url('account/signout')?>"><?=__('Sign out', true)?></a></li>
			</ul>
		</li>
	</ul>
	<script> jQuery(document).ready(Page.bindUserMenu); </script>
<? } else { ?>
	<ul class="nav nav-pills pull-right">
		<li><a href="<?=root_url('/about')?>"><?=__('ABOUT US', true)?></a></li>
		<li><a href="<?=root_url('/contact')?>"><?=__('CONTACT', true)?></a></li>
		<li class="login"><a href="<?=root_url('account/signin')?>"><?=__('LOGIN', true)?></a></li>	
	</ul>
<? } ?>

