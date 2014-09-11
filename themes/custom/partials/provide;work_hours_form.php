<div id="work_hours_general">

	<?=form_dropdown('general_select_date', array(
		Core_Locale::date('A_weekday_1').' - '.Core_Locale::date('A_weekday_7'),
		Core_Locale::date('A_weekday_1').' - '.Core_Locale::date('A_weekday_5'),
		Core_Locale::date('A_weekday_1').' - '.Core_Locale::date('A_weekday_6'),
		__('Weekends only'), __('Specific days')), '', 'class="span3 general_select" ')?> 

	<?=__('from %s to %s', array(
		form_dropdown('general_select_start', Phpr_DateTime::time_array(), '08:00:00', 'class="span3 general_select_start"'),
		form_dropdown('general_select_end]', Phpr_DateTime::time_array(), '18:00:00', 'class="span3 general_select_end"')
	))?>

</div>

<div id="work_hours_specific" style="display:none">
	<? for ($x = 0; $x < 7; $x++):  // Do seven times ?>
	<div class="row-fluid" id="weekday_<?=$x+1?>">
		<div class="span3 day_column">
			<label class="checkbox"><?=form_checkbox('schedule_'.$x.'_checkbox', true)?><?=Core_Locale::date('A_weekday_'.($x+1))?></label>
		</div>
		<div class="span9">
			<div class="hide"><?=__('Select to add your availability for %s', Core_Locale::date('A_weekday_'.($x+1)))?></div>
			<div class="show">
				<?=__('from %s to %s', array(
					form_dropdown('Provider[schedule_'.$x.'_start]', Phpr_DateTime::time_array(array(null=>'--:--')), form_value($provider, 'schedule_'.$x.'_start', '08:00:00'), 'class="span3"'),
					form_dropdown('Provider[schedule_'.$x.'_end]', Phpr_DateTime::time_array(array(null=>'--:--')), form_value($provider, 'schedule_'.$x.'_end', '18:00:00'), 'class="span3"')
				))?> 
				<? if ($x==0): ?>
					<a href="javascript:;" id="link_apply_all"><?=__('Apply to all')?></a>
				<? endif ?>
			</div>
		</div>
	</div>
	<? endfor ?>
</div>