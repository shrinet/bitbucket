<?	
	if (!isset($category) || !$category) {
		$category = Bluebell_Request::get_category_from_role_name(post('role_name', $role_name));
	}
?>

<? if ($category && $category->form): ?>
	
	<div class="row-fluid request-section">
		<div class="span1 visible-desktop align-right">
			<span class="ring-badge ring-badge-info"><i class="fa fa-info"></i></span>
		</div>
		<div class="span10">
			<?= $category->form->display_form(array(
				'data' => $request->custom_form_data,
				'field_array_name' => 'Custom',
				'field_classes' => array(
					'text' => 'span12',
					'textarea' => 'span12',
				)
			)) ?>
		</div>
	</div>

	<div class="row-fluid request-section">
		<div class="span1 visible-desktop align-right">
			<span class="ring-badge ring-badge-info"><i class="fa fa-pencil"></i></span>
		</div>
		<div class="span10">

			<div class="control-group description">
				<label for="request_description" class="control-label"><?= __('Anything else the %s should know?', $category->name) ?></label>
				<textarea id="request_description" 
					name="Request[description]" 
					rows="5"
					class="span12"><?=$request->description?></textarea>

				<script> $('#request_description').autogrow(); </script>

				<!-- Uploaded thumbnails -->
				<?= $this->display_partial('request:request_form_files') ?>

			</div>

		</div>
	</div>

<? else: ?>

	<div class="row-fluid request-section">
		<div class="span1 visible-desktop align-right">
			<span class="ring-badge ring-badge-info"><i class="fa fa-pencil"></i></span>
		</div>
		<div class="span10">

			<div class="control-group description">
				<label for="request_description" class="control-label"><?= __('What do you need done?') ?></label>
				<textarea id="request_description" 
					name="Request[description]" 
					rows="5"
					placeholder="<?= __('Please describe your request. The more detail you provide, the better quality quotes.') ?>"
					class="span12"><?=$request->description?></textarea>

				<script> $('#request_description').autogrow(); </script>

				<!-- Uploaded thumbnails -->
				<?= $this->display_partial('request:request_form_files') ?> 

			</div>

		</div>
	</div>

<? endif ?>