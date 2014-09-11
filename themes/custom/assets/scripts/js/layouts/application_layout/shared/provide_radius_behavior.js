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

	$.widget("ahoy.provideRadius", {
		version: '1.0.2',
		options: {
			radiusMax:       100,
			radiusUnit:      'km',
			radiusId:        '#provider_service_radius',// Radius value
			mapId:           '#work_radius_map',        // Map object (see map behavior)
			addressFieldId:  '#work_radius_address',    // Address field used for geocoding
			areaListId:      '#work_radius_area_list',  // Unordered list to populate with nearby areas
			onComplete:      null                       // Callback after lookup is complete
		},

		// Internals
		_nearby_postcodes: [],
		_country: null,
		_postcode: null,
		_radius: 25,

		_map: null,
		_address_field: null,
		_address: null,
		_latlng: null,
		_area_list: null,

		_init: function() { var self = this;

			if ($(this.options.radiusId).length)
				this._radius = $(this.options.radiusId).val();

			this._map = $(this.options.mapId);
			this._address_field =  $(this.options.addressFieldId);
			this._area_list = $(this.options.areaListId);

			// Map
			self._map.gmap({ 
				distance_type: this.options.radiusUnit,
				allow_drag: false,
				allow_scrollwheel: false,
				alow_dbl_click_zoom: false
			});

			// Slider
			self.bind_slider();

			// Detect address changes
			self._address_field.change(function() { self.populate_address($(this).val()); });

		},

		bind_slider: function() { var self = this;

			$('#work_radius_slider').slider({
				step:5,
				min:1,
				max:parseInt(self.options.radiusMax)+1,
				value: self._radius,
				change:function(event,ui) {
					
					if (self._address) {
						self._radius = (ui.value <= 1) ? 1 : ui.value-1;
						self._map
							.gmap('clearCircles')
							.gmap('showCircleAtAddressObject', self._address, self._radius)
							.gmap('autofit');
						$('#work_radius_radius span').text(self._radius);
						$(self.options.radiusId).val(self._radius);

						self.find_nearby_areas(self._postcode, self._country, self._radius);
					}

				},
				slide:function(event,ui) {
					var radius = (ui.value <= 1) ? 1 : ui.value-1;
					$('#work_radius_radius span').text(radius);
				}
			});

		},

		populate_address: function(address_string) { var self = this;

			if ($.trim(address_string)=="") {
				self.disable_control();
				return
			}

			$.waterfall(
				function() { 
					var deferred = $.Deferred(); 
					self._map.gmap('getObjectFromAddressString', address_string, {
						callback: function(address) { 
							self._address = address;
							self._latlng = self._map.gmap('getLatLngFromAddressObject', self._address);
							deferred.resolve();
						}
					});
					return deferred;
				},
				function() {
					if (!self._address)
						return $.Deferred().resolve(); 
						
					self._map
						.gmap('alignToAddressObject', self._address)
						.gmap('addMarkerFromAddressObject', self._address, 'provider_tag')
						.gmap('showCircleAtAddressObject', self._address, self._radius)
						.gmap('autofit');

					self._country = self._map.gmap('getValueFromAddressObject', self._address, 'country');
					self._postcode = self._map.gmap('getValueFromAddressObject', self._address, 'postal_code');
					return $.Deferred().resolve(); 
				},
				function() {
					if (!self._address)
						return $.Deferred().resolve(); 
						
					self.enable_control();
					
					self.find_nearby_areas(self._postcode, self._country, self._radius);
					return $.Deferred().resolve(); 
				}
			);
		},

		find_nearby_areas: function(postcode, country, radius) { var self = this;
			self._nearby_postcodes = []; // Reset main postal code array
			var cache = {}; // Cache for duplicates

			$.phpr.post()
				.action('bluebell:on_nearby_areas')
				.data({country:country, postcode:postcode, radius:radius, unit:self.options.radiusUnit})
				.success(function(response) { 
					var result = $.parseJSON(response.html);
					if (result && result.postalCodes) {
						self._area_list.empty();
						$.each(result.postalCodes, function(key, area){
							// Add unique post code to array
							var duplicate_exists = false;
							for (var i=0; i < self._nearby_postcodes.length; i++) { 
								if (self._nearby_postcodes[i]==area.postalcode)
									duplicate_exists = true;
							}
							if (!duplicate_exists)
								self._nearby_postcodes.push(area.postalcode);

							// Remove duplicates, add to area list
							if (!cache[area.adminCode1+area.name]) {
								cache[area.adminCode1+area.name] = true;
								var item = $('<li />').text(area.name+', '+area.adminCode1).appendTo(self._area_list);
							}
						});
					}
					else
						self._area_list.empty().append($('<li />').text(self._area_list.attr('data-empty-text')));

					// End point
					self.options.onComplete && self.options.onComplete(self);

				}).send();
		},

		get_nearby_postcodes: function() {
			return this._nearby_postcodes;
		},

		disable_control: function() {
			$('#work_radius_disabled').fadeIn();
			$('#work_radius').addClass('disabled');
		},

		enable_control: function() {
			$('#work_radius_disabled').fadeOut();
			$('#work_radius').removeClass('disabled');
		}


	})

})( jQuery, window, document );
