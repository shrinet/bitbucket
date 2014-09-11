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
 * Role select behavior
 */

var Page = (function(page, $) {

	page.bindRoleSelect = function(element, options) {
		element = $(element);
		var defaults = {
			onSelect: null, // Callback after selection
			onChange: null // Callback after any change
		};
		options = $.extend(true, defaults, options);

		element.autocomplete({
			minLength: 2,
			source: function(request, response) {
				element.phpr().post()
					.action('service:on_search_categories')
					.data('search', request.term)
					.success(function(requestObj){
						var json = [],
							result = $.parseJSON(requestObj.html);

						$.each(result, function(k,v){ 
							if (v.parent_id) {
								// Only add child categories
								json.push(v.name); 
							}
						});

						if (json)
							response(json);

						return;
					}).send();
			},
			select: function(event, ui) {
				options.onSelect && options.onSelect(self, ui.item.value);
			},
			change: function(event, ui) {
				options.onChange && options.onChange(self, element.val());
			}
		});
	}

	return page;
}(Page || {}, jQuery));