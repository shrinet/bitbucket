var Page = (function(page, $){

	page.constructor = $(function() {
		page.initAssist();
		page.initCategorySuggest();
		page.populateCategories();
	});

	/**
	 * Suggest new category
	 */
	page.initCategorySuggest = function() {
		$('#popup_suggest_category').popup({ 
			trigger: '#link_suggest_category',
			onOpen: function() {
				$('#suggest_category_name').val('');
			}
		});

		page.requestSuggestFormFields.validate('#assist_suggest_form')
			.action('service:on_suggest_category')
			.success(function() {
				$('#popup_suggest_category').popup('closePopup');
				$('#popup_suggest_category_success').popup({ autoReveal: true });
			});
	}

	/**
	 * Assist logic
	 */ 

	page.initAssist = function() {

		// Form validation
		page.requestAssistFormFields.validate('#assist_form').action('bluebell:on_review_request');

		// Assist sub nav
		$('#filter_navigation a').click(function() {
			$(this).parent().siblings().removeClass('active').end().addClass('active');
			
			if ($(this).hasClass('category')) {
				$('#category_form_select_category').show();
				$('#category_form_select_alpha').hide();
			} else {
				$('#category_form_select_category').hide();
				$('#category_form_select_alpha').show();
			}

			return false;
		});
		
		// Assist selection
		$('#select_parent').change(function(){
			var parent_id = $(this).val();
			$('#select_category').empty();
			$('#select_alpha option').each(function() {
				if ($(this).attr('data-parent-id') == parent_id)
					$('#select_category').append($(this).clone());
			});
		});

		$('#select_category, #select_alpha').change(function(){
			$('#request_category_id').val($(this).val());
			$('#request_title').val($(this).children('option:selected').text());
		});
	}

	page.populateCategories = function() {

		$('#select_parent, #select_category, #select_alpha').empty();
		$('#select_parent').phpr().post()
			.action('service:on_search_categories')
			.loadIndicator(false)
			.success(function(response){
				var result = $.parseJSON(response.html);
				$.each(result, function(k,v){ 
					if (v.parent_id) 
						$('<option />').val(v.id).text(v.name).attr('data-parent-id',v.parent_id).appendTo($('#select_alpha'));
					else
						$('<option />').val(v.id).text(v.name).appendTo($('#select_parent'));
				});
			}).send();

	}

	return page;
}(Page || {}, jQuery));
