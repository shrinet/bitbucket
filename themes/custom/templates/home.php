<!DOCTYPE html>
<!--[if IE 8]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"><!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width">
	<title><?=h($this->page->title_name) ?> - <?=c('site_name')?></title>
	<?=$this->display_partial('site:head')?>
</head>
<body class="<?=$this->page->template_code?> <?=$this->page->page_code?>">

		<div class="header">
			<?=$this->display_partial('site:header')?>
		</div<!--.header-->
		<div class="slider container-fluid">
			<?=$this->display_partial('site:slider')?>
		</div><!--slider-->
		<div class="container-fluid" id="middle-menu">
			<?=$this->display_partial('site:midmenu')?>
		</div>
		<div class="container-fluid" id="home-mes">
			<?=$this->display_partial('site:hmessage')?>
		</div><!--#home-mes-->
		<div class="container-fluid" id="home-midtext">
			<?=$this->display_partial('site:midtext')?>
		</div><!--#home-midtext-->
		<div class="container-fluid" id="wombidslocation">
			<?=$this->display_partial('site:location')?>
		</div><!--#wombidslocation-->
		<div class="container-fluid" id="howwork">
			<?=$this->display_partial('site:works')?>
		</div><!--#howwork-->
		<div class="container-fluid" id="cspeak">
			<?=$this->display_partial('site:testimonials')?>
		</div><!--#cspeak-->
		<div class="wrapper" id="site-footer">
			<div class="container">
				<?=$this->display_partial('site:footer')?>
			</div>
		</div><!--.footer-->	
</body>
</html>