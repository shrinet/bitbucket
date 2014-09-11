<?
	$container_id = (isset($container_id)) ? $container_id : 'provider_portfolio';
?>
<? if (isset($images) && count($images) > 0): ?>
<div id="<?=$container_id?>" class="carousel slide">
	<ol class="carousel-indicators">
		<?
			$count = 0; 
		?>
		<? foreach ($images as $item): $count++; ?>
			<li data-target="#<?=$container_id?>" data-slide-to="<?=$count?>" class="<?=$count==1?'active':''?>"></li>
		<? endforeach ?>
	</ol>
	<div class="carousel-inner">
		<?
			$count = 0; 
		?>	
		<? foreach ($images as $item): $count++; ?>
			<div class="<?=$count==1?'active':''?> item">
				<img src="<?=$item->image?>" alt="" data-image-id="<?=$item->id?>" data-image-thumb="<?=$item->thumb?>" />
			</div>
		<? endforeach ?>
	</div>
	<a class="carousel-control left" href="#<?=$container_id?>" data-slide="prev">&lsaquo;</a>
	<a class="carousel-control right" href="#<?=$container_id?>" data-slide="next">&rsaquo;</a>	
</div>

<? elseif (isset($is_manage) && $is_manage): ?>
	<div class="well" id="panel_button_profile_portfolio">
		<a href="javascript:;" class="button_profile_portfolio"><?=__('Click to add portfolio photos')?></a>
	</div>
	<div id="<?=$container_id?>"></div>
<? endif ?>
<script>
	jQuery(document).ready(function($) {
		$('#<?=$container_id?>').portfolio();
	});
</script>