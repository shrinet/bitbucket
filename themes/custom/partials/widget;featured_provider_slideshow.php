<? /* You can customise the homepage images with a featured group. 
		1. Select Service > Providers > Provider groups 
		2. Create a new group called Featured, name the API Code: featured
		3. Assign providers who have good portfolios */ 
?>
<div id="featured-providers" class="carousel slide">

	<? if ($featured_providers): ?>

		<!-- Featured providers -->
		<ol class="carousel-indicators">
			<?
				$count = 0;
			?>
			<? foreach ($featured_providers as $portfolio): $count++; ?>
				<li data-target="#featured-providers" data-slide-to="<?=$count?>" class="<?=$count==1?'active':''?>"></li>
			<? endforeach ?>
		</ol>
		<div class="carousel-inner">
			<?
				$count = 0;
			?>
			<? foreach ($featured_providers as $portfolio): $count++; ?>
				<div class="<?=$count==1?'active':''?> item">
					<img src="<?=$portfolio->image?>" alt="" />
					<div class="carousel-caption">
						<h4><?=$portfolio->provider->business_name?></h4>
						<p><?=$portfolio->provider->location_string?></p>
					</div>
				</div>
			<? endforeach ?>
		</div>

	<? else: ?>

		<!-- Sample content -->
		<ol class="carousel-indicators">
			<li data-target="#featured-providers" data-slide-to="0" class="active"></li>
			<li data-target="#featured-providers" data-slide-to="1"></li>
			<li data-target="#featured-providers" data-slide-to="2"></li>
		</ol>
		<div class="carousel-inner">
			<div class="active item">
				<img src="<?=theme_url('assets/images/demo/plumber.jpg')?>" alt="" />
				<div class="carousel-caption">
					<h4>Plumbers</h4>
					<p><?=__('Find a Plumber near you')?></p>
				</div>
			</div>
			<div class="item">
				<img src="<?=theme_url('assets/images/demo/chef.jpg')?>" alt="" />
				<div class="carousel-caption">
					<h4>Chefs</h4>
					<p><?=__('How about a Chef for your next party')?></p>
				</div>
			</div>
			<div class="item">
				<img src="<?=theme_url('assets/images/demo/mechanic.jpg')?>" alt="" />
				<div class="carousel-caption">
					<h4>Mechanics</h4>
					<p><?=__('Get a local Mechanic to fix your car')?></p>
				</div>
			</div>
		</div>	
		
	<? endif ?>
	<a class="carousel-control left" href="#featured-providers" data-slide="prev">&lsaquo;</a>
	<a class="carousel-control right" href="#featured-providers" data-slide="next">&rsaquo;</a>
</div>