<?
	$has_portfolio = ($provider&&$provider->portfolio->count > 0);	
?>
<div id="panel_photos" style="<?=($has_portfolio)?'':'display:none'?>">
	<ul class="thumbnails">
		<? if ($has_portfolio): ?>
			<? foreach ($provider->get_portfolio() as $item): ?>
				<li>
					<div class="thumbnail" data-image-id="<?=$item->id?>">
						<img src="<?=$item->thumb?>" alt="" />
						<a href="javascript:;" class="remove">Remove</a>
					</div>
				</li>
			<? endforeach ?>
		<? endif ?>
	</ul>
</div>
<p><?=__('%s to your portfolio', '<a href="javascript:;" id="link_add_photos">'.__('Attach photos', true).'</a>')?></a>
<input id="input_add_photos" type="file" name="portfolio[]" multiple>
