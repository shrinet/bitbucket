<? foreach ($provider->testimonials as $testimonial): ?>
	<div class="row-fluid">
		<div class="span10 columns">
			<blockquote>
				"<?=$testimonial->comment?>"
				<cite><?=$testimonial->name?>, <?=$testimonial->location?></cite>
			</blockquote>
		</div>
	</div>
	<div class="pull-right">
		<a href="javascript:;" class="button_delete_testimonial" 
			data-provider-id="<?=$provider->id?>" 
			data-testimonial-id="<?=$testimonial->id?>" 
			data-confirm="<?=__('Are you sure you want to delete this testimonial?')?>">
			<?=__('Delete this testimonial')?>
		</a>
	</div>    
<? endforeach ?>