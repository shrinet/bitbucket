<div class="well">
	<div class="row-fluid">
		<div class="span4">

			<!-- Basic information -->
			<h6><?= __('Who you are looking for') ?></h6>
			<p><?= $request->title ?></p>
			<h6><?= __('When do you need them') ?></h6>
			<p><?= Bluebell_Request::required_by($request) ?></p>
			<h6><?= __('Where do you need them') ?></h6>
			<p><?= Bluebell_Request::location($request) ?></p> 

		</div>
		<div class="span6">

			<? if ($custom_form_fields = Bluebell_Request::get_custom_form_fields($request)): ?>
				
				<!-- Custom form details -->
				<? foreach ($custom_form_fields as $field): ?>
					<h6><?= $field->label ?></h6>
					<?= $field->current_value ?>
				<? endforeach ?>
				<h6><?= __('Extra information') ?></h6>
				<?=$request->description_html?>

			<? else: ?>

				<!-- Regular form details -->
				<h6><?= __('What you need done') ?></h6>
				<?=$request->description_html?>

			<? endif ?>

		</div>
		<div class="span2">

			<!-- Edit request link -->
			<a href="<?= root_url('request/edit') ?>" id="edit_request" 
				class="pull-right btn btn-primary btn-icon">
				<i class="icon-pencil"></i> 
				<?= __('Edit request') ?>
			</a>

		</div>
	</div>
</div>