<div class="row-fluid">
	<div class="control-group">
		<label for="provider_description_experience" class="control-label"><?= __('Describe your work experience') ?></label>
		<textarea name="Provider[description_experience]" value="<?php if(!form_value($provider, 'description_experience')) { echo form_value($provider, 'description'); } else { echo form_value($provider, 'description_experience'); } ?>" class="span12" id="provider_description_experience"><?php if(!form_value($provider, 'description_experience')) { echo form_value($provider, 'description'); } else { echo form_value($provider, 'description_experience'); } ?></textarea>
	</div>

	<div class="control-group">
		<label for="provider_description_speciality" class="control-label"><?= __('What kind of projects do you work on?') ?></label>
		<textarea name="Provider[description_speciality]" value="<?= form_value($provider, 'description_speciality') ?>" class="span12" id="provider_description_speciality"></textarea>
	</div>

	<div class="control-group">
		<label for="provider_description_why_us" class="control-label"><?= __('Why do your customers like working with you?') ?></label>
		<textarea name="Provider[description_why_us]" value="<?= form_value($provider, 'description_why_us') ?>" class="span12" id="provider_description_why_us"></textarea>
	</div>
</div>