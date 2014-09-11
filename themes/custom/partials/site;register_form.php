<!-- Auto Login Flag -->
<input type="hidden" name="user_auto_login" value="1" />

<div class="control-group">
	<div class="controls">
		<label for="user_email" class="control-label"><?= __('Email Address', true) ?></label>
		<input type="text" name="User[email]" value="" id="user_email" class="span12" />
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<label for="user_username" class="control-label"><?= __('Full name', true) ?></label>
		<input type="text" name="User[name]" value="" id="user_username" class="span12" />
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<label for="user_password" class="control-label"><?= __('Create a Password', true) ?></label>
		<input type="password" name="User[password]" value="" id="user_password" class="span12" />
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<label for="user_password_confirm" class="control-label"><?= __('Confirm Password', true) ?></label>
		<input type="password" name="User[password_confirm]" value="" id="user_password_confirm" class="span12" />
	</div>
</div>

<script>

Page.siteRegisterFormFields = $.phpr.form().defineFields(function() {
	this.defineField('User[email]').required("<?=__('Please specify your email address',true)?>").email("<?=__('Please specify a valid email address',true)?>").action('user:on_validate_email', "<?=__('Email address is already in use', true)?>");
	this.defineField('User[name]').required("<?=__('Please specify Full name',true)?>");
	this.defineField('User[password]').required("<?=__('Please specify a new password',true)?>");
	this.defineField('User[password_confirm]').required("<?=__('Please specify a new password',true)?>").matches('#user_password', "<?=__('Password and confirmation password do not match.',true)?>");
});

</script>