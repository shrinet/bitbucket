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
 * Provider work hours behavior
 */


;(function ($, window, document, undefined) {

	$.widget("utility.provideHours", {
		version: '1.0',
		options: {
			specific_container:'#work_hours_specific', 
			general_container:'#work_hours_general', 
			general_select_class: 'general_select',
			toggle_link:'#link_work_hours', 
			apply_all_link:'#link_apply_all',
			row_selector:'div.row-fluid',
			use_general: true
		},

		toggle_link: null,
		apply_all_link: null,
		specific: null,
		general: null,
		general_select: null,
		general_select_start: null,
		general_select_end: null,

		_init: function() { var self = this;

			this.toggle_link = $(self.options.toggle_link);
			this.apply_all_link = $(self.options.apply_all_link);
			this.specific = $(self.options.specific_container);
			this.general = $(self.options.general_container);
			this.general_select = self.general.find('select.'+self.options.general_select_class);
			this.general_select_start = self.general.find('select.'+self.options.general_select_class+'_start');
			this.general_select_end = self.general.find('select.'+self.options.general_select_class+'_end');
			if (self.options.use_general) {

				// Toggle link
				self.toggle_link.click($.proxy(self.specific_toggle, self));

				// Bind general hours
				self.general_select.change(function() { self.select_general_days($(this).val()); });
				self.select_general_days(self.general_select.val());

				// Automatically apply all for general time dropdowns
				self.general_select_start.change(function() { $('#weekday_1').find('select:first').val($(this).val()); self.click_apply_all(); });
				self.general_select_end.change(function() { $('#weekday_1').find('select:last').val($(this).val()); self.click_apply_all(); });

			} else {
				self.specific.show();
				self.general.hide();
				self.validate_times();
			}

			// Apply all
			self.apply_all_link.click($.proxy(self.click_apply_all, self));
			
			// Bind specific hours		
			self.check_all_days();
			self.specific.find(self.options.row_selector + ' input[type=checkbox]').change(function() { self.check_day($(this).closest(self.options.row_selector)); });
		

		},
		
		check_all_days: function() { var self = this;
			self.specific.find(self.options.row_selector).each(function() { self.check_day($(this)); });
		},

		check_day: function(row_element) { var self = this;
			row_element = $(row_element);
			var checkbox = row_element.find('input[type=checkbox]');

			if (checkbox.is(':checked'))
				row_element.find('div.hide').hide().end().find('div.show').show();
			else {
				row_element.find('div.hide').show().end().find('div.show').hide().end().find('select').val(0);
			}
		},

		select_general_days: function(value) { var self = this;
			switch (value) {
				case '0': // Mon - Sun
					$('#weekday_1, #weekday_2, #weekday_3, #weekday_4, #weekday_5, #weekday_6, #weekday_7').find('input[type=checkbox]').attr('checked', true);				
				break;			
				case '1': // Mon - Fri
					$('#weekday_1, #weekday_2, #weekday_3, #weekday_4, #weekday_5').find('input[type=checkbox]').attr('checked', true);
					$('#weekday_6, #weekday_7').find('input[type=checkbox]').attr('checked', false);
				break;
				case '2': // Mon - Sat
					$('#weekday_1, #weekday_2, #weekday_3, #weekday_4, #weekday_5, #weekday_6').find('input[type=checkbox]').attr('checked', true);
					$('#weekday_7').find('input[type=checkbox]').attr('checked', false);
				break;
				case '3': // Sat - Sun
					$('#weekday_6, #weekday_7').find('input[type=checkbox]').attr('checked', true);
					$('#weekday_1, #weekday_2, #weekday_3, #weekday_4, #weekday_5').find('input[type=checkbox]').attr('checked', false);
				break;
				case '4':
					self.general_select.val(0);
					self.specific_toggle();
				break;
			}
			self.check_all_days();
		},

		specific_toggle: function() { var self = this;
			self.general.toggle();
			self.specific.toggle();
			self.toggle_link.toggleText();
		},

		click_apply_all: function() { var self = this;
			var start_val = $('#weekday_1').find('select:first').val();
			var last_val = $('#weekday_1').find('select:last').val();

			self.specific.find(self.options.row_selector).each(function() { 
				$(this).find('select:first:visible').val(start_val);
				$(this).find('select:last:visible').val(last_val);
			});
		},

		validate_times: function() { var self = this;
			self.specific.find(self.options.row_selector).each(function() { 
				var start = Math.round($(this).find('select:first').val().replace(/:/g, ''));
				var end = Math.round($(this).find('select:last').val().replace(/:/g, ''));
				if (start >= end)
					$(this).find('input[type=checkbox]').attr('checked', false);
				else
					$(this).find('input[type=checkbox]').attr('checked', true);
			});
		}

	})

})( jQuery, window, document );

