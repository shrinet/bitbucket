<?
	$current_menu = isset($current_menu) ? $current_menu : '';
?>
<ul class="nav nav-list">
	<li class="nav-header"><?=__('About', true)?></li>
	<li class="<?=$current_menu=='about'?'active':''?>"><a href="<?=root_url('about')?>"><?=__('About %s', c('site_name'), true)?></a></li>
	<li class="<?=$current_menu=='blog'?'active':''?>"><a href="<?=root_url('blog')?>"><?=__('%s Blog', c('site_name'), true)?></a></li>
	<li class="<?=$current_menu=='faq'?'active':''?>"><a href="<?=root_url('faq')?>"><?=__('FAQ', true)?></a></li>
	<li class="divider"></li>
	<li class="nav-header"><?=__('Legal', true)?></li>
	<li class="<?=$current_menu=='terms'?'active':''?>"><a href="<?=root_url('terms')?>"><?=__('Terms and Conditions', true)?></a></li>
	<li class="<?=$current_menu=='privacy'?'active':''?>"><a href="<?=root_url('privacy')?>"><?=__('Privacy Policy', true)?></a></li>
	<li class="divider"></li>
	<li class="nav-header"><?=__('Contact', true)?></li>
	<li class="<?=$current_menu=='contact'?'active':''?>"><a href="<?=root_url('contact')?>"><?=__('Contact Us', true)?></a></li>
</ul>