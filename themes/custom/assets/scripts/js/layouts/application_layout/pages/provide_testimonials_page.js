/**
 * Provide Testimonial
 */

var Page = (function(page, $){

	page.constructor = $(function() {

		page.provideTestimonialWriteFormFields.validate('#form_provide_testimonial_write')
			.action('service:on_create_testimonial')
			.success(function() {
				$('#p_provide_testimonial_write_form').hide();
				$('#provide_testimonial_write_success').show();
			});

	});

	return page;
}(Page || {}, jQuery));