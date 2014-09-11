<div class="control-group">
	<label for="testimonial_name" class="control-label"><?= __('Your Name') ?></label>
	<div class="controls">
		<input type="text" name="Testimonial[name]" value="" id="testimonial_name" class="span12" />
	</div>
</div>

<div class="control-group">
	<label for="testimonial_location" class="control-label"><?= __('City, State') ?></label>
	<div class="controls">
		<input type="text" name="Testimonial[location]" value="" id="testimonial_location" class="span12" />
	</div>
</div>

<div class="control-group">
	<label for="testimonial_comment" class="control-label"><?= __('Your testimonial') ?></label>
	<div class="controls">
		<textarea name="Testimonial[comment]" class="span12" id="testimonial_comment"></textarea>
	</div>
</div>

<script>

Page.provideTestimonialWriteFormFields = $.phpr.form().defineFields(function() {
	this.defineField('Testimonial[name]').required("<?=__('Please provide your name')?>");
	this.defineField('Testimonial[location]').required("<?=__('Please specify your location')?>");
	this.defineField('Testimonial[comment]').required("<?=__('Please provide a testimonial')?>");
});

</script>