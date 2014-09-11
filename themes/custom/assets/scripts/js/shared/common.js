/**
 * Scripts Ahoy! Software
 *
 * Copyright (c) 2012-2013 Scripts Ahoy! (scriptsahoy.com)
 * All terms, conditions and copyrights as defined
 * in the Scripts Ahoy! License Agreement
 * http://www.scriptsahoy.com/license
 *
 */


// 
// Validation
// 

PHPR.validateDefaults.errorElement = 'span';

PHPR.validateDefaults.highlight = function(element) {
	$(element).closest('.control-group')
		.removeClass('success').addClass('error');
};

PHPR.validateDefaults.success = function(element) {
	element.closest('.control-group')
		.removeClass('error').addClass('success');
	//.text('OK!').addClass('valid')
};

PHPR.validateDefaults.errorPlacement = function(error, element) {
	var controlsContainer = element.closest('.controls');
	if (controlsContainer.length > 0)
		element = controlsContainer;
	
	element.after(error.addClass('help-block validate-text'));
};

//
// Utility loading indicator
//

var UtilityLoadingIndicator = function(options) { 

	var o = {};

	o.show = function(message) {
		var message = 'Processing...';
		$(document).statusbar('option', { position: 'top', context: 'info', time: 999999, message:message });
	}

	o.hide = function() {
		$(document).statusbar('removeMessage');
	}

	return o;
};


PHPR.postDefaults.customIndicator = UtilityLoadingIndicator;

PHPR.post.popupError = function(requestObj) {
	$(document).statusbar('option', { position: 'top', context: 'error', time: 10000, message: requestObj.errorMessage });
};

/**
 * Auto init expander plugin
 */

jQuery(document).ready(function($) { 
	$('.expander').each(function() { 
		var slice_at = $(this).data('slice-point');
		var expand_text = $(this).data('expand-text');
		$(this).expander({
			slicePoint: slice_at,
			expandText: expand_text,
			userCollapseText: ''
		});
	});
});

/**
 * "Click Outside" plugin
 */

 $.fn.extend({
	// Calls the handler function if the user has clicked outside the object (and not on any of the exceptions)
	clickOutside: function(handler, exceptions) {
		var $this = this;

		$("body").bind("click", function(event) {
			if (exceptions && $.inArray(event.target, exceptions) > -1) {
				return;
			} else if ($.contains($this[0], event.target)) {
				return;
			} else {
				handler(event, $this);
			}
		});

		return this;
	}
});
