<div class="container">
<? 
	$logo = Cms_Config::get_logo();
?>
<? if ($logo): ?>
	<div class="row-fluid">
		<div class="span6">
		
			<div class="logo">
			
				<a href="<?=root_url('/')?>"><img src="<?=$logo?>" alt="<?=c('site_name')?>" /></a>
				<!--span class="hide-for-small motto"><?=__('Get the job done your way!', true)?></span-->
				
			</div>
		</div>
		<div class="span6">
			<div id="site-user-menu">
				<?=$this->display_partial('site:user_menu')?>
			<!-- add manual code from here -->
		
<!--ul class="nav nav-pills pull-right">
  <li class=""><a href="<?=root_url('/intro')?>">HOW IT WORKS</a></li>
  <li><a href="<?=root_url('/about')?>">ABOUT US</a></li>
  <li><a href="<?=root_url('/contact')?>">CONTACT</a></li>
  <li class="login"><a href="<?= root_url('account/signin') ?>">LOGIN</a></li>
</ul-->

<!-- end code till here -->	
			</div>
		</div>
	</div>
<? else: ?>
	<div class="logo">
		<h1 class="text"><a href="<?=root_url('/')?>"><?=c('site_name')?></a></h1>
	</div>
	<div class="motto hidden-phone"><?=__('Get the job done your wayd!', true)?></div>
	<div id="site-user-menu">
		<?=$this->display_partial('site:user_menu')?>
	</div>
<? endif ?>
</div>
</div><!-- .container-->