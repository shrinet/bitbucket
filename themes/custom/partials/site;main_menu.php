<?
	$is_provider = ($this->user && $this->user->is_provider && !$this->user->is_requestor);
	$is_requestor = ($this->user && $this->user->is_requestor && !$this->user->is_provider);
	$is_newuser = !$this->user || ($this->user && !$this->user->is_requestor && !$this->user->is_provider);
	$is_hybrid = ($this->user && $this->user->is_requestor && $this->user->is_provider);

	// The $current_menu variable should be defined in the Pre Load Code field of all pages in the following way:
	// $this->data['current_menu'] = 'home';
	//
	$current_menu = isset($current_menu) ? $current_menu : '';
?>
<div class="navbar navbar-inverse">
	<div class="navbar-inner">
		
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>
 
		<a class="brand visible-phone" href="#"><?=c('site_name')?></a>

		<div class="nav-collapse collapse">

			<ul class="nav">
				<li class="<?=$current_menu=='dash'?'active':''?>">
					<? if ($is_hybrid): ?>
						<a href="<?=root_url('/dashboard')?>"><?=__('Dashboard', true)?></a>
					<? elseif ($is_requestor): ?>
						<a href="<?=root_url('/dashboard')?>"><?=__('My Requests', true)?></a>
					<? elseif ($is_provider): ?>
						<a href="<?=root_url('/dashboard')?>"><?=__('My Jobs', true)?></a>
					<? elseif ($this->user): ?>
						<a href="<?=root_url('/dashboard')?>"><?=__('Dashboard', true)?></a>
					<? else: ?>
						<a href="<?=root_url('/')?>"><?=__('Home', true)?></a>
					<? endif ?>
				</li>

				<? if ($is_provider||$is_hybrid): ?>
					<li class="<?=$current_menu=='provide'?'active':''?>"><a href="<?=root_url('provide/profiles')?>"><?=__('Manage Profile', true)?></a></li>
				<? endif ?>

				<? if ($is_requestor||$is_hybrid): ?>
					<li class="<?=$current_menu=='request'?'active':''?>"><a href="<?=root_url('request')?>"><?=__('Request Service', true)?></a></li>
				<? endif ?>

				<? if ($is_newuser): ?>
					<li class="<?=$current_menu=='intro'?'active':''?>"><a href="<?=root_url('intro')?>"><?=__('How It Works', true)?></a></li>
					<li class="<?=$current_menu=='request'?'active':''?>"><a href="<?=root_url('request')?>"><?=__('Request Service', true)?></a></li>
					<li class="<?=$current_menu=='provide'?'active':''?> visible-phone"><a href="<?=root_url('provide')?>"><?=__('Provide a Service', true)?></a></li>
				<? endif?>
			</ul>
			<? if ($is_newuser): ?>
				<ul class="nav pull-right">
					<li class="hidden-phone"><a href="<?=root_url('provide')?>">
						<?=__('I want to provide my services', true)?> 
					</a></li>
				</ul>
			<? endif?>

		</div>

	</div>
</div>
