<div class="row-fluid">
	<fieldset class="form-horizontal">
		<? if (!$this->user): ?>
			<div class="control-group">
				<label for="suggest_name" class="control-label"><?= __('Your name') ?></label>
				<div class="controls">
					<input type="text" name="name" id="suggest_name" class="span11" />
				</div>
			</div>

			<div class="control-group">
				<label for="suggest_email" class="control-label"><?= __('Your email') ?></label>
				<div class="controls">
					<input type="text" name="email" id="suggest_email" class="span11" />
				</div>
			</div>
		<? endif ?>

		<div class="control-group">
			<label for="suggest_category_name" class="control-label"><?= __('Suggested category name') ?></label>
			<div class="controls">
				<input type="text" name="category_name" id="suggest_category_name" class="span11" />
			</div>
		</div>
	</fieldset>
</div>

<script>

Page.requestSuggestFormFields = $.phpr.form().defineFields(function() {
	<? if (!$this->user): ?>
		this.defineField('email').required("<?=__('Please specify your email address',true)?>").email("<?=__('Please specify a valid email address',true)?>");
		this.defineField('name').required("<?=__('Please specify your name')?>");
	<? endif ?>
	this.defineField('category_name').required("<?=__('Please specify a service name')?>");
});

</script>
