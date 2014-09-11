/**
 * Account
 */

var Page = (function(page, $){

	page.constructor = $(function() {
		page.bindWorkHistory();
		page.bindNotifications();
	});
	
	page.toggleEdit = function(element) {		
		jQuery(element).closest('.block').find('.view:first').slideToggle().next('.edit').slideToggle();
	}

	page.initCountrySelect = function(element) {
		element = jQuery(element);
		var state_field = element.closest('div.control-group').next('div.control-group').find('select:first');

		element.countrySelect({ 
			stateElementId: state_field,
			afterUpdate: function(obj) { 
				obj.stateElement = element.parents('div.control-group:first').next('div.control-group').find('select:first');
			}
		});
		
	}

	page.filterHistory = function(mode, submode, pageNo) {
		var post_obj = $('#form_work_history').phpr().post()
			.action('bluebell:on_account_filter_history')
			.update('#work_history', 'account:work_history');

		if (mode)
			post_obj = post_obj.data('mode', mode);

		if (submode)
			post_obj = post_obj.data('submode', submode);

		if (pageNo)
			post_obj = post_obj.data('page', pageNo);

		post_obj.send();
	}

	page.bindWorkHistory = function() {
		$('#mode_filter a').live('click', function() {
			var mode = $(this).data('mode');
			page.filterHistory(mode, null, null);
		});
		
		$('#submode_filter_offers a').live('click', function() {
			var submode = $(this).data('submode');
			page.filterHistory(null, submode, null);
		});

		$('#p_site_pagination a').live('click', function() {
			var pageNo = $(this).data('page');
			page.filterHistory(null, null, pageNo);
			return false;
		});
	}

	page.bindNotifications = function() {
		$('#form_user_notifications').submit(function() {
			$('#form_user_notifications').phpr().post().action('user:on_update_preferences').send();
			return false;
		});
	}

	return page;
}(Page || {}, jQuery));