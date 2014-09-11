<div class="row-fluid">
	<div class="span8"><?=__('What is the estimated price range for this work?')?></div>
	<div class="span2 price_column">
		<span class="currency"><?=Core_Locale::currency_symbol()?></span>
		<input type="text" name="Quote[onsite_price_start]" value="<?= form_value($quote, 'onsite_price_start') ?>" class="onsite_price span1" />
		<span class="divider">-</span>
	</div>
	<div class="span2 price_column">
		<span class="currency"><?=Core_Locale::currency_symbol()?></span>
		<input type="text" name="Quote[onsite_price_end]" value="<?= form_value($quote, 'onsite_price_end') ?>" class="onsite_price span1" />
	</div>
</div>
<div class="row-fluid">
	<div class="span8"><?=__('Do you charge a fee to travel to the work site?')?></div>
	<div class="span2">
		<label class="radio" for="quote_onsite_travel_required_yes">
			<?=form_radio('Quote[onsite_travel_required]', true, form_value_boolean($quote, 'onsite_travel_required', true), 'id="quote_onsite_travel_required_yes"')?> Yes
		</label>
	</div>
	<div class="span2">
		<label class="radio" for="quote_onsite_travel_required_no">
			<?=form_radio('Quote[onsite_travel_required]', false, form_value_boolean($quote, 'onsite_travel_required', false, true), 'id="quote_onsite_travel_required_no"')?> No
		</label>
	</div>
</div>
<div id="panel_travel_fee" style="display:none">
	<div class="row-fluid">
		<div class="span8"><?=__('How much do you charge for travel?')?></div>
		<div class="span2">&nbsp;</div>
		<div class="span2 price_column">
			<span class="currency"><?=Core_Locale::currency_symbol()?></span>
			<?=form_input('Quote[onsite_travel_price]', form_value($quote, 'onsite_travel_price'), 'class="span1"')?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span8"><?=__('Will you waive the travel fee if you are picked for this job?')?></div>
		<div class="span2">
			<label class="radio" for="quote_onsite_travel_waived_true">
				<?=form_radio('Quote[onsite_travel_waived]', true, form_value_boolean($quote, 'onsite_travel_waived', true), 'id="quote_onsite_travel_waived_true"')?> Yes
			</label>
		</div>
		<div class="span2">
			<label class="radio" for="quote_onsite_travel_waived_false">
				<?=form_radio('Quote[onsite_travel_waived]', false, form_value_boolean($quote, 'onsite_travel_waived', false, true), 'id="quote_onsite_travel_waived_false"')?> No
			</label>
		</div>
	</div>
</div>

<script>

Page.jobQuoteOnSiteFormFields = $.phpr.form().defineFields(function() {
	this.defineField('Quote[onsite_price_end]').requiredMulti(1, '.onsite_price', "<?=__('Please enter an estimated price')?>");
	this.defineField('Quote[onsite_travel_price]').required("<?=__('Please enter how much you charge for travel')?>");
});

</script>