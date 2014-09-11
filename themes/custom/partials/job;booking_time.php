<div class="row-fluid">
	<div class="span4"><p class="detail_label"><?=__('Start time')?></p></div>
	<div class="span8">
		<p class="detail_text">
			<? if ($quote->start_at): ?>
				<?=$quote->start_at->to_time_format()?> <?=$quote->start_at->to_long_date_format()?> 
				<a href="javascript:;" id="link_booking_time_suggest"><?=__('Suggest another time')?></a>
			<? else: ?>
				<?=__('No appointment time yet')?> 
				<a href="javascript:;" id="link_booking_time_suggest"><?=__('Suggest a time')?></a>
			<? endif ?>
		</p>
	</div>
</div>

<div class="booking_time_suggest" id="booking_time_suggest_container" style="display:none">
	<?=form_open(array('id' => 'form_booking_time', 'class' => 'form_booking_time'))?>
		<input type="hidden" name="quote_id" value="<?= $quote->id ?>" />
		<div class="row-fluid">
			<div class="span8">
				<div class="control-group select_booking_time_time">
					<?=form_dropdown('start_time', Phpr_DateTime::time_array(), '09:00:00', 'class="booking_time_time span12" id="booking_time_time"')?>
				</div>
				<div class="control-group field_booking_time_date">
					<?=form_widget('start_date', array(
						'class' => 'Db_DatePicker_Widget',
						'field_id' => 'booking_time_date',
						'field_name' => 'start_date',
						'on_select' => '$(this).closest("form").validate().element(this)',
						'css_class' => 'span12'
					))?>
				</div>
			</div>
			<div class="span4"><?=form_submit('suggest_time', __('Suggest'), 'class="btn btn-primary btn-block"')?></div>
		</div>
	<?=form_close()?>
</div>

<script>

 Page.jobBookingTimeFormFields = $.phpr.form().defineFields(function(){
	this.defineField('start_date').required("<?=__('Please provide a start date',true)?>");
	this.defineField('start_time').required("<?=__('Please provide a start time',true)?>");
 });

</script>