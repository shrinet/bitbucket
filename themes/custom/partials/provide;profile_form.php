<div class="provider-form-control">
	<div class="control-group business_name">
		<label for="provide_business_name" class="control-label"><?= __('Click to add company name') ?></label>
		<div class="controls edit"><?=form_input('Provider[business_name]', form_value($provider, 'business_name'), 'placeholder="'.__('Click to add company name').'" class="span12 oversize" id="provide_business_name"')?></div>
	</div>
	<div class="business_logo">
		<?
			$has_logo = ($provider&&$provider->logo->count > 0);
			$logo = $blank_logo = theme_url('assets/images/avatar_thumb.jpg');
			if ($provider)
				$logo = $provider->get_logo('autox60', $logo);
		?>
		<img src="<?=$logo?>" id="provide_logo" alt="" style="display:<?=($has_logo)?'block':'none'?>" data-blank-src="<?=$blank_logo?>" />
		<a href="javascript:;" id="link_provide_logo" style="display:<?=($has_logo)?'none':'block'?>"><?=__('Click here to add your business logo')?></a>
		<a href="javascript:;" id="link_provide_logo_remove" style="display:<?=($has_logo)?'block':'none'?>"><?=__('Remove')?></a>
		<input id="input_provide_logo" type="file" name="logo">
	</div>
	<div class="row-fluid">
		<div class="span4">
			<div class="profile_photo">
				<?
					$has_photo = ($provider&&$provider->photo->count > 0);
					$photo = $blank_photo = theme_url('assets/images/avatar_thumb.jpg');
					if ($provider)
						$photo = $provider->get_photo(100, $photo);
				?>
				<img src="<?=$photo?>" id="provide_photo" alt="" data-blank-src="<?=$blank_photo?>" />
				<a href="javascript:;" id="link_provide_photo" class="btn btn-block" style="display:<?=($has_photo)?'none':'block'?>"><?=__('Add Image')?></a>
				<a href="javascript:;" id="link_provide_photo_remove" style="display:<?=($has_photo)?'block':'none'?>"><?=__('Remove')?></a>
				<input id="input_provide_photo" type="file" name="photo">
			</div>
		</div>
		<div class="span8">
			<div class="control-group role_name">
				<label for="provide_role_name" class="control-label"><?= __('trade, speciality or skill') ?></label>
				<div class="controls edit"><?=form_input('Provider[role_name]', form_value($provider, 'role_name'), 'placeholder="'.__('trade, speciality or skill').'" class="span12" id="provide_role_name"')?></div>
			</div>
			<div class="row-fluid lic">
				<div class="span6">
					<div class="license">
						<label for="provide_license" class="control-label"><?= __('License #', true) ?></label>
						<div class="controls edit"><?=form_input('Provider[license]', form_value($provider, 'license'), 'placeholder="'.__('License #').'" class="span12" id="provide_license"')?></div>
					</div>					
				</div>
				<div class="span6">
					<div class="phone">
						<label for="provide_lic_state" class="control-label"><?= __('State', true) ?></label>
						<div class="controls edit"><?=form_input('Provider[licn_state]', form_value($provider, 'licn_state'), 'placeholder="'.__('State').'" class="span12" id="provide_lic_state"')?></div>
					</div>					
				</div>
			</div>
			<div class="row-fluid lic">
				<div class="span6">
					<div class="phone">
						<label for="provide_lic_type" class="control-label"><?= __('Type', true) ?></label>
						<div class="controls edit"><?=form_input('Provider[type]', form_value($provider, 'type'), 'placeholder="'.__('Type').'" class="span12" id="provide_lic_type"')?></div>
					</div>					
				</div>
				<? $date = form_value($provider, 'date_issue');
					$dt = new DateTime($date);
			?>
				<div class="span6">
					<div class="phone">
						<label for="provide_lic_date" class="control-label"><?= __('Date Issued', true) ?></label>
						<div class="controls edit"><?=form_widget('date_issue', array(
												'class' => 'Db_DatePicker_Widget',
												'css_class' => 'date_issue span12',
												'field_id' => 'provide_lic_date',
												'field_value' => $dt->format('m/d/Y'),
												'field_name' => 'Provider[date_issue]',
												'allow_past_dates' => true,
												'on_select' => '$(this).closest("form").validate().element(this)'
											))?></div>
					</div>					
				</div>
			</div>
			<div class="control-group Bonded">
				<label for="provide_lic_bonded" class="control-label"><?= __('Bonded (Y/N Amount)', true) ?></label>
				<div class="controls edit"><?=form_input('Provider[bonded]', form_value($provider, 'bonded'), 'placeholder="'.__('Bonded (Y/N Amount)').'" class="span12" id="provide_lic_bonded"')?></div>
			</div>					
		</div>
	</div>
	<div class="control-group phone_info" id="profile_details_phone">
			<div class="row-fluid">
				<div class="span6">
					<div class="phone">
						<label for="provide_phone" class="control-label"><?= __('Phone number', true) ?></label>
						<div class="controls edit"><?=form_input('Provider[phone]', form_value($provider, 'phone'), 'placeholder="'.__('Phone number').'" class="span12" id="provide_phone"')?></div>
					</div>					
				</div>
				<div class="span6">
					<div class="mobile">
						<label for="provide_mobile" class="control-label"><?= __('Mobile number', true) ?></label>
						<div class="controls edit"><?=form_input('Provider[mobile]', form_value($provider, 'mobile'), 'placeholder="'.__('Mobile number').'" class="span12" id="provide_mobile"')?></div>
					</div>
				</div>
			</div>
	</div>
	<div class="control-group address" id="profile_details_address">
		<label for="provide_address" class="control-label"><?= __('Business address') ?></label>
		<div class="controls edit">
			<div class="controls">
				<?=form_input('Provider[street_addr]', form_value($provider, 'street_addr'), 'placeholder="'.__('Business address').'" class="span12" id="provide_address"')?>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="controls"><?=form_input('Provider[city]', form_value($provider, 'city'), 'placeholder="'.__('City', true).'" class="span12" id="provide_city"')?></div>
				</div>
				<div class="span6">
					<div class="controls"><?=form_input('Provider[zip]', form_value($provider, 'zip'), 'placeholder="'.__('Zip / Postal Code', true).'" class="span12" id="provide_zip"')?></div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<div class="controls"><?=form_dropdown('Provider[country_id]', Location_Country::get_name_list(), form_value($provider, 'country_id'), 'id="provide_country_id"',  __('-- Select --', true))?></div>
				</div>
				<div class="span6">
					<div class="controls"><?=form_dropdown('Provider[state_id]', Location_State::get_name_list(form_value($provider, 'country_id')), form_value($provider, 'state_id'), 'id="provide_state_id"', __('-- Select --', true));?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="control-group url">
		<label for="provide_url" class="control-label"><?= __('Website link') ?></label>
		<div class="controls edit">
			<?=form_input('Provider[url]', form_value($provider, 'url'), 'placeholder="'.__('Website link').'" class="span12" id="provide_url"')?>
		</div>
	</div>
</div>

<script>

Page.provideProfileDetailsFormFields = $.phpr.form().defineFields(function(){
	this.defineField('Provider[business_name]').required("<?=__('Please specify your business name')?>");
	this.defineField('Provider[role_name]').required("<?=__('Please enter a skill or occupation',true)?>").action('service:on_validate_category_name', "<?=__('Please select a valid service form the list', true)?>");
	this.defineField('Provider[phone]').phone("<?=__('Please specify a valid phone number',true)?>");
	this.defineField('Provider[mobile]').phone("<?=__('Please specify a valid phone number',true)?>");
	this.defineField('Provider[street_addr]').required("<?=__('You must provide your business address')?>");
	this.defineField('Provider[zip]').required("<?=__('Please specify a zip / postal code')?>");
	this.defineField('Provider[country_id]').required("<?=__('Please select your country')?>");
	this.defineField('Provider[state_id]').required("<?=__('Please select your state')?>");
	this.defineField('Provider[url]').url("<?=__('Please enter a valid website link')?>");
});


</script>