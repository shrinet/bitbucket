<!-- Who do you need? -->
<div class="row-fluid request-section">
	<div class="span1 visible-desktop align-right">
		<span class="ring-badge ring-badge-info"><i class="fa fa-user"></i></span>
	</div>
	<div class="span10">
		<div class="control-group title">
			<label for="request_title" class="control-label"><?=__('Who do you need?')?></label> 
			<div class="controls">
				<input type="text" id="request_title" 
					name="Request[title]" 
					value="<?= $role_name ?>" 
					placeholder="<?= __('Handyman, Personal Trainer, Plumber, Cleaner, etc.') ?>" 
					class="span12" />
			</div>
		</div>
	</div>
</div>

<!-- When should they start? -->
<div class="row-fluid request-section">
	<div class="span1 visible-desktop align-right">
		<span class="ring-badge ring-badge-info"><i class="fa fa-clock-o"></i></span>
	</div>
	<div class="span10">
		<div class="control-group required_by">
			<label class="control-label"><?= __('When should they start?') ?></label>
			<div class="controls">
				<ul class="radio-group" id="required_by_group">
					<li>
						<!-- Flexible -->
						<label for="request_required_by_flexible" class="radio">
							<input type="radio" id="request_required_by_flexible" 
								name="Request[required_by]" 
								<?= radio_state($request->required_by, Bluebell_Request::required_by_flexible) ?> 
								value="<?= Bluebell_Request::required_by_flexible ?>" />
							<?=__("I'm flexible", true)?>
						</label>
						<div class="radio-expand">
							<?=form_dropdown('Request[required_type]', array(
								Bluebell_Request::required_type_flexible => __('anytime',true), 
								Bluebell_Request::required_type_flexible_week => __('this week',true), 
								Bluebell_Request::required_type_flexible_month => __('this month',true)
							), $request->required_type)?>
						</div>
					</li>
					<li>
						<!-- Urgent! -->
						<label for="request_required_by_urgent" class="radio urgent">
							<input type="radio" id="request_required_by_urgent" 
								name="Request[required_by]" 
								<?= radio_state($request->required_by, Bluebell_Request::required_by_urgent) ?> 
								value="<?= Bluebell_Request::required_by_urgent ?>" />
							<?=__("Within 48 hours (It's urgent!)", true)?>
						</label>
						<div class="radio-expand">
							<p>
								<span class="label label-important"><i class="fa fa-bullhorn"></i> <?=__('Request providers available to respond today!')?></span>
							</p>
						</div>
					</li>
					<li>
						<!-- At a specific date and time -->
						<label for="request_required_by_firm" class="radio">
							<input type="radio" id="request_required_by_firm" 
								name="Request[required_by]" 
								<?= radio_state($request->required_by, Bluebell_Request::required_by_firm) ?> 
								value="<?= Bluebell_Request::required_by_firm ?>" />
							<?=__('At a specific date and time', true)?>
						</label>
						<div class="radio-expand firm">
							<div class="well">

								<!-- Primary time -->
								<div class="control-group">
									<div class="controls">
										<div class="firm-date-primary">
											<?=form_widget('firm_date', array(
												'class' => 'Db_DatePicker_Widget',
												'css_class' => 'firm-date span3',
												'field_id' => 'request_firm_date',
												'field_name' => 'firm_date',
												'allow_past_dates' => false,
												'on_select' => '$(this).closest("form").validate().element(this)'
											))?>
											<?=__('between %s and %s', array(
												form_dropdown('firm_time_start', Phpr_DateTime::time_array(), '09:00:00'),
												form_dropdown('firm_time_end', Phpr_DateTime::time_array(), '10:00:00')
											))?> 
										</div>
									</div>
								</div>

								<!-- Add an alternative time -->
								<div class="firm-date-add">
									<i class="fa fa-plus" data-toggle-class="fa fa-minus"></i>
									<a href="javascript:;" id="link_request_alt_time" data-toggle-text="<?=__('%s alternative time', __('Remove',true))?>">
										<?=__('%s alternative time', __('Add',true))?>
									</a> 
									<?=__('to increase the number of quotes')?>
								</div>

								<!-- Secondary time -->
								<div class="control-group">
									<div class="controls">
										<div class="firm-date-secondary" style="display:none">
											<?=form_widget('firm_date_alt', array(
												'class' => 'Db_DatePicker_Widget',
												'css_class' => 'firm-date span3',
												'field_id' => 'request_firm_date_alt',
												'field_name' => 'firm_date_alt',
												'allow_past_dates' => false,
												'on_select' => '$(this).closest("form").validate().element(this)'
											))?>
											<?=__('between %s and %s', array(
												form_dropdown('firm_time_alt_start', Phpr_DateTime::time_array(), '09:00:00'),
												form_dropdown('firm_time_alt_end', Phpr_DateTime::time_array(), '10:00:00')
											))?> 
										</div>
									</div>
								</div>

							</div>
						</div>

					</li>
				</ul>
			</div>
		</div>

	</div>
</div>

<!-- Where do you need them? -->
<div class="row-fluid request-section">
	<div class="span1 visible-desktop align-right">
		<span class="ring-badge ring-badge-info"><i class="fa fa-map-marker"></i></span>
	</div>
	<div class="span10">

		<div class="control-group location">
			<label for="request_location" class="control-label"><?= __('Where do you need them?') ?></label>
			<div class="controls">
				<input type="text" id="request_location" 
					name="Request[address]" 
					value="<?= (!empty($_POST['zipcode']) ? $_POST['zipcode'] : $request->address_string) ?>"
					placeholder="<?= __('Enter your postal/zip code or full address') ?>"
					class="span12" />

				<label class="checkbox location-remote" for="location_remote">
					<input type="checkbox" id="location_remote"
						name="Request[is_remote]" 
						<?= checkbox_state($request->is_remote) ?>
						value="1" />
					<?=__('Job can be performed remotely')?>
				</label>
			</div>
		</div>

	</div>
</div>

<!-- What do you need done? / Custom builder form -->
<div id="p_request_custom_description">
	<?= $this->display_partial('request:custom_description') ?>
</div>

<script>

Page.requestSimpleFormFields = $.phpr.form().defineFields(function() {
	this.defineField('Request[title]').required("<?=__('Please enter a skill or occupation',true)?>");
	this.defineField('Request[address]').required("<?=__('Please provide a valid location',true)?>").action('location:on_validate_address', "<?=__('Please provide a valid location', true)?>");
	this.defineField('Request[description]').required("<?=__('Please describe your request',true)?>").minWords(5, "<?=__('Can you be more descriptive?', true)?>");
	this.defineField('firm_date').required("<?=__('Please select a date',true)?>").date("<?=__('Please enter a valid date',true)?>");
	this.defineField('firm_date_alt').required("<?=__('Please select a date',true)?>").date("<?=__('Please enter a valid date',true)?>");
});

</script>
