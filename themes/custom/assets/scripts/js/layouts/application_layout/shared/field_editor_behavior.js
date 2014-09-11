/**
 * Scripts Ahoy! Software
 *
 * Copyright (c) 2012 Scripts Ahoy! (scriptsahoy.com)
 * All terms, conditions and copyrights as defined
 * in the Scripts Ahoy! License Agreement
 * http://www.scriptsahoy.com/license
 *
 */

/**
 * Profile field editing behavior
 * 
 * Hot swappable label / fields.
 * 
 * HTML Example:
 * 
 *  <div class="control-group name">
 *      <!-- The label must come before the div.control-group edit container -->
 *      <label for="name" class="control-label">Your name</div>
 *      <div class="controls edit">
 *          <?=form_input('name', '', 'placeholder="Your name please" id="name"')?>
 *      </div>
 *  </div>
 * 
 * Usage:
 * 
 * $('#form_or_container').fieldEditor();
 * 
 */

;(function ($, window, document, undefined) {

	$.widget("utility.fieldEditor", {
		version: '1.0.0',
		options: {
			form_id: '',
			edit_selector: 'div.edit',           // Edit input container
			field_selector: 'div.control-group', // Form field container
			on_set_field: null,                  // Callback when field is set, for single field use event: field_editor.field_set
			implode_char: ', ',                  // The character to separate multiple inputs
			single_validate: true                // Validate fields one at a time
		},

		form: null,
		active_field: null,
		is_loaded: null,

		_init: function() { var self = this;
			this.form = $(this.options.form_id);

			this.reset_profile_fields();

			// Bind click label field
			// returns false to stop clickOutside plugin
			this.element.find('label').click(function() {            
				self.click_profile_field(this);
				return false;
			});

			self.populate_data();

			self.is_loaded = true;
		},

		populate_data: function() { var self = this;
			this.element.find(this.options.edit_selector).each(function() { self.set_profile_field($(this)); });
		},

		click_profile_field: function(obj) { var self = this;
			
			// If a field is currently being edited, 
			// set it or prevent other field edits
			if (this.active_field) {
				if (this.set_profile_field(this.active_field)===false)
					return;
			}
			
			// Find the edit container, show, focus and cache
			var edit = $(obj).hide().parent().find(self.options.edit_selector).show();
			edit.find('input:first').focus(); 
			self.active_field = edit;

			// Use clickOutside plugin for autosave
			setTimeout(function() { edit.clickOutside(function() { self.set_profile_field(edit); }) }, 1);
			
			// Detect keypress events
			edit.find('input').bind('keydown', function(e){ 
				var code = (e.keyCode ? e.keyCode : e.which);

				if (code == 13) // ENTER
				{
					e.preventDefault();
					self.set_profile_field($(this).closest(self.options.edit_selector));
					return false;
				}
				else if (code == 9) // TAB
				{
					e.preventDefault();
					var next_field;
					var field = $(this).closest(self.options.field_selector);

					// Revert to standard tabbing for fields with multiple inputs
					if (field.find('input').length > 1)
						return;
					
					self.set_profile_field($(this).closest(self.options.edit_selector));

					if (e.shiftKey) 
						next_field = self.dom_shift(field, self.options.field_selector, -1); // Move back
					else
						next_field = self.dom_shift(field, self.options.field_selector, 1); // Move forward

					next_field.find('label').trigger('click');
					return false;
				}

			});
		},

		// Find the next / previous field regardless of its DOM position
		dom_shift: function(obj, selector, shift) { var self = this;
			var all_fields = self.element.find(selector);
			var field_index = all_fields.index(obj);
			var next_field = all_fields.eq(field_index + shift);
			return next_field;
		},

		// Hides all fields and shows their labels
		reset_profile_fields: function() { var self = this;
			self.element.find('label').each(function() { 
				$(this).show().parent().find(self.options.edit_selector).hide();     
			});
		},

		// Determine a friendly value to display, 
		// if we have multiple fields string them together nicely
		get_field_value: function(inputs) { var self = this;
			var value = "";
			if (inputs.length > 1) {
				jQuery.each(inputs, function(k,v) {
					if ($.trim($(this).val()) != "") {
						if ($(this).is('select')) {
							value += (k==0) ? $(this).find('option:selected').text() : self.options.implode_char + $(this).find('option:selected').text();
						} else {
							value += (k==0) ? $(this).val() : self.options.implode_char + $(this).val();
						}
					}
				});
			}
			else
				value = $.trim(inputs.first().val());

			return value;
		},

		// Set a profile field, must return true
		// If fields do not validate, return false
		set_profile_field: function(obj) { var self = this;

			self.reset_profile_fields();
			
			var inputs = obj.find('input, select');
			var field = inputs.first();
			var label = field.closest('.edit').prev('label');
			var value = self.get_field_value(inputs);

			// Has the field been populated or emptied? 
			// Display value or placeholder for latter
			if (value != "") 
				label.text(value).addClass('populated');
			else
				label.text(field.attr('placeholder')).removeClass('populated');

			// Reset cache variable
			self.active_field = null;
			
			// Cancel clickOutside plugin
			$('body').unbind('click');

			// Events
			if (self.is_loaded) {
				obj.trigger('field_editor.field_set', [value]);
				self.options.on_set_field && self.options.on_set_field(self, obj, value);
			}

			// Validates true
			return true;
		},

		// Apply validation to our fields using an ahoy.validate code
		// (Implement Wins: Flawless victory)
		apply_validation: function(form, formObj, on_success) { var self = this;

			formObj.validate(form, {
				
				onfocusout: false, // Removed because it looks messy
				ignore: null, // Do not ignore hidden fields

				errorPlacement: function(error, element) {
					element.after(error.addClass('help-block validate-text'));
				},

				success: function(element) { 
					element.addClass('valid');
					// Check all elements for errors before removing error
					// class on group
					var group = element.closest('.control-group');

					if (group.find('.validate-text.error').not('.valid').length == 0) {
						group.removeClass('error');
						element.remove();
					}

				},

				// Binding to the invalid handler here instead 
				// of errorPlacement so we don't conflict
				invalidHandler: function(form, validator) {
					var errors = validator.numberOfInvalids();
					if (errors) {
						// Only show the first error
						var el = $(validator.errorList[0].element);

						// Target the label that should perfectly preceed the .edit container
						var label = el.closest('div.edit').prev().trigger('click');

						if (self.options.single_validate) {
							// Trash the other error labels because they are annoying
							setTimeout(function() { self.clean_other_invalid_fields(el); }, 1);
						}
					}
				}

			}).success(on_success);
		},

		clean_other_invalid_fields: function(el) {
			var error_label = el.closest('.control-group').find('span.error.validate-text');
			$('span.error.validate-text').not(error_label)
				.closest('.control-group').removeClass('error')
				.end().remove();
		}
		
	})

})( jQuery, window, document );