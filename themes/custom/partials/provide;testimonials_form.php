<div class="row-fluid">
	<div class="control-group">
		<label for="testimonial_email" class="control-label"><?= __('To Email') ?></label>
		<div class="controls">
			<input type="text" name="Testimonial[email]" value="" id="testimonial_email" class="span12" />
		</div>
	</div>

	<div class="control-group">
		<label for="testimonial_subject" class="control-label"><?= __('Subject') ?></label>
		<div class="controls">
			<input type="text" name="Testimonial[subject]" value="<?= __('Add a testimonial for me on %s', c('site_name')) ?>" id="testimonial_subject" class="span12" />
		</div>
	</div>

	<div class="control-group">
		<label for="testimonial_message" class="control-label"><?= __('Message') ?></label>
		<div class="controls">
			<textarea name="Testimonial[message]" class="span12" id="testimonial_message"><?= __('Would you add a brief recommendation of my work for my %s profile? Please let me know if you have any questions and thanks for your help!', c('site_name')) ?></textarea>
		</div>
	</div>

</div>

<script>

Page.provideTestimonialsFormFields = $.phpr.form().defineFields(function(){
	this.defineField('Testimonial[email]').required("<?=__('Please specify an email address')?>");
	this.defineField('Testimonial[subject]').required("<?=__('Please specify a subject')?>");
	this.defineField('Testimonial[message]').required("<?=__('Please specify message')?>");
});

</script>