<?
	$can_edit = isset($can_edit) ? $can_edit : true;
?>
<div class="row-fluid">
	<div class="span6">
		<div class="request_time">
			<h4><?=__('Requested appointment time(s)')?></h4>
			<p><?=Bluebell_Request::required_by($request)?></p>
		</div>
	</div>
	<div class="span6">
		<div class="request_location">
			<h4><?=__('Location', true)?></h4>
			<p>
				<?=Bluebell_Request::location($request)?><br />				
				<? if ($request->location_string): ?>
					<a href="<?=Location_Map::get_map($request)?>" target="_blank" class="small">
						<i class="icon-map-marker"></i> 
						<?=__('Show map', true)?>
					</a>
				<? endif ?>
			</p>
		</div>
	</div>
</div>

<!-- Request Description -->
<div class="request_description">

	<? if ($custom_form_fields = Bluebell_Request::get_custom_form_fields($request)): ?>
		
		<!-- Custom form details -->
		<? foreach ($custom_form_fields as $field): ?>
			<h4><?= $field->label ?></h4>
			<?= $field->current_value ?>
		<? endforeach ?>
		<h4><?= __('Extra information') ?></h4>
		<?=$request->description_html?>

	<? else: ?>

		<!-- Regular form details -->
		<h4><?=__('What you need')?></h4>
		<?=$request->description_html?>

	<? endif ?>
	
</div>


<div class="added_info">
	<? foreach ($request->get_extra_description() as $extra): ?>
		<? 
			$extra_date = new Phpr_DateTime($extra->created_at); 
		?>
		<blockquote>
			<p>
				<?=$extra->description?>
				<small><?=__('Additional information added %s ago', Phpr_DateTime::interval_to_now($extra_date))?></small>
			</p>
		</blockquote>
	<? endforeach ?>
</div>
<? if ($can_edit): ?>
	<p>
		<a href="javascript:;" id="link_add_description" 
			data-toggle-text="<?=__('%s more information', __('Remove',true))?>" 
			data-toggle-class="remove" 
			class="small">
			<?=__('%s more information', __('Add',true))?>
		</a>
	</p>
	<div id="panel_add_description" style="display:none">
		<?=form_open(array('id' => 'add_description_form'))?>
			<input type="hidden" name="request_id" value="<?= $request->id ?>" />
			<div class="control-group">
				<div class="controls">
					<textarea name="description" class="span12"></textarea>
				</div>
			</div>
			<?=form_submit('submit', __('Save', true), 'class="btn btn-small"')?>
			<a href="javascript:;" id="link_add_description_cancel"><?=__('Cancel', true)?></a>
		<?=form_close()?>
		<script>
			Page.addDescriptionFormFields = $.phpr.form().defineFields(function(){
					this.defineField('description', 'Extra Description').required("<?= __('Please enter more request details') ?>");
				}).validate('#add_description_form')
				.action('service:on_describe_request')
				.update('#p_details', 'request:manage_details');
		</script>
	</div>
<? endif ?>

<!-- Photos -->
<h4><?=__('Photos', true)?></h4>
<?=form_open(array('id' => 'form_add_photos'))?>
	<input type="hidden" name="request_id" value="<?= $request->id ?>" />
	<div id="panel_photos" class="well" style="<?=(count($request->files)) ? '' : 'display:none'?>">
		<ul class="thumbnails">
			<? foreach ($request->files as $file): ?>
				<li>
					<div class="thumbnail" data-image-id="<?=$file->id?>">
						<img src="<?=$file->getThumbnailPath(100, 75, true, array('mode'=>'crop'))?>" alt="" />
						<a href="javascript:;" class="remove">Remove</a>
					</div>
				</li>
			<? endforeach ?>
		</ul>
	</div>
	<? if ($can_edit): ?>
		<p><?=__('%s about this job', '<a href="javascript:;" id="link_add_photos">'.__('Attach photos', true).'</a>')?></a>
		<input id="input_add_photos" type="file" name="files[]" multiple>
	<? endif ?>
<?=form_close()?>
<script>
	jQuery(document).ready(Page.bindAddPhotos);
</script>
