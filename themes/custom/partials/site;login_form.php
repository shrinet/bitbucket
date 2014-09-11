<div class="control-group">
	<div class="controls">
		<label for="user_login" class="control-label"><?= __('Email', true) ?></label>
		<input type="text" name="User[login]" value="" id="user_login" class="span12" />
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<label for="user_password" class="control-label"><?= __('Password', true) ?></label>
		<input type="password" name="User[password]" value="" id="user_password" class="span12" />
	</div>
</div>

<script>

Page.siteLoginFormFields = $.phpr.form().defineFields(function() {
	this.defineField('User[login]').required("<?=__('Please specify your username or email address',true)?>");
	this.defineField('User[password]').required("<?=__('Please specify your password',true)?>").action('user:on_validate_login', "<?=__('Password entered is invalid',true)?>");
});

</script>