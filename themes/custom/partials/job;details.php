<h4><?=__('Job Details')?></h4>

<? if ($custom_form_fields = Bluebell_Request::get_custom_form_fields($request)): ?>
	<!-- Custom form details -->
	<? foreach ($custom_form_fields as $field): ?>
		<div class="row-fluid">
			<div class="span4 mobile-span3"><p class="detail"><?= h($field->label) ?>:</p></div>
			<div class="span8 mobile-span9"><p><?= h($field->current_value) ?></p></div>
		</div>
	<? endforeach ?>
<? endif ?>

<!-- Primary Job Descriptions -->
<div class="job-description expander" data-slice-point="250" data-expand-text="<?=__('Show more', true)?>">
	<?= $request->description_html ?>
</div>

<!-- Extra Job Descriptions -->
<? foreach ($request->get_extra_description() as $extra): ?>
	<? 
		$extra_date = new Phpr_DateTime($extra->created_at); 
	?>
	<blockquote>
		<p>
			<div class="job-description expander" data-slice-point="250" data-expand-text="<?=__('Show more', true)?>">
				<?= h($extra->description) ?>
			</div>
			<small><?=__('Additional information added %s ago', Phpr_DateTime::interval_to_now($extra_date))?></small>
		</p>
	</blockquote>
<? endforeach ?>

<? if ($request->files->count): ?>
	<h4><?=__('Job Photos')?></h4>

	<!-- Photos -->
	<ul class="thumbnails">
		<? foreach ($request->files as $file): ?>
			<li class="span4">
				<div class="thumbnail">
					<a href="#full-image" data-toggle="modal" 
						onclick="$('#full-image-element').attr('src', '<?=$file->getThumbnailPath(990, 660, true, array('mode'=>'crop'))?>')">
						<img src="<?=$file->getThumbnailPath(100, 75, true, array('mode'=>'crop'))?>" alt="" />
					</a>
				</div>
			</li>		
		<? endforeach ?>
	</ul>

	<!-- Modal for viewing photos -->
	<div class="modal hide fade in" id="full-image">
		<div class="modal-body align-center">
			<a class="close" data-dismiss="modal">×</a>
			<img id="full-image-element" src="<?= theme_url('assets/images/ajax_loading.gif') ?>" alt="">
		</div>
	</div>
<? endif ?>