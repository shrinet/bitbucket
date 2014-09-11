<?
	$role = isset($role) ? $role : false;
?>
<div class="row-fluid">
	<? if ($role): ?>
		<input type="hidden" name="Request[category_id]" value="<?= $role->id ?>" />
		<input type="hidden" name="Request[role_name]" value="<?= $role->name ?>" />
	<? else: ?>
		<section class="control-group title">
			<label for="request_role_name" class="control-label"><?=__('Who do you need?')?></label>
			<input type="text" name="Request[role_name]" value="" class="span12" id="request_role_name" placeholder="<?= __('Handyman, Plumber, Cleaner, etc.') ?>" />
		</section>
	<? endif ?>

	<section class="control-group location">
		<label for="request_location" class="control-label"><?=__('Where do you need this service?')?></label>
		<input type="text" name="Request[address]" value="" class="span12" id="request_location" placeholder="<?= __('Postal/zip code or full address') ?>" />
	</section>

	<section class="control-group description" style="position:relative">
		<label for="request_description" class="control-label"><?=__('What do you need done?')?></label>
		<textarea name="Request[description]" class="span12" id="" id="request_description" placeholder="<?= __('Please describe your request. The more detail you provide, the better quality quotes.') ?>"></textarea>        
	</section>

	<? if ($this->user): ?>
		<input type="hidden" name="Request[user_id]" value="<?= $this->user->id ?>" />
	<? else: ?>
	<section class="control-group email">
		<label for="email_address" class="control-label"><?=__('Enter your email address')?></label>
		<input type="text" name="User[email]" value="" class="span12" id="email_address" placeholder="<?= __('Enter your email address for providers to contact you') ?>" />
	</section>
	<? endif ?>
</div>

<script>

Page.requestQuickFormFields = $.phpr.form().defineFields(function(){
<? if (!$role): ?>
	this.defineField('Request[role_name]', 'Role Name').required("<?=__('Please enter a skill or occupation',true)?>").action('service:on_validate_category_name', "<?=__('Please select a valid service form the list', true)?>");
<? endif ?>
	this.defineField('Request[address]', 'Address').required("<?=__('Please provide a valid location',true)?>").action('location:on_validate_address', "<?=__('Please provide a valid location', true)?>");
	this.defineField('Request[description]', 'Description').required("<?=__('Please describe your request',true)?>").minWords(5, "<?=__('Can you be more descriptive?', true)?>");
<? if (!$this->user): ?>
	this.defineField('User[email]', 'Email').required("<?=__('Please specify your email address',true)?>").email("<?=__('Please specify a valid email address',true)?>").action('user:on_validate_email', "<?=__('Email address is already in use', true)?>");
<? endif ?>

});

</script>