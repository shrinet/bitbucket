<?=form_open(array('id'=>'form_user_notifications', 'class'=>'nice custom'))?>

	<div class="row-fluid notification_title">
		<div class="span8"><h4><?=__('General Notifications')?></h4></div>
		<div class="span4">
			<ul class="block-grid grid-span2 hidden-phone">
				<li><?=__('Email', true)?></li>
				<li><?=__('SMS', true)?></li>
			</ul>
		</div>
	</div>

	<?=$this->display_partial('account:notification_block', array('title' => __('Job scheduled'), 'email_code' => 'service_email_job_booked'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Appointment time reminder'), 'email_code' => 'service_email_booking_reminder', 'sms_code' => 'service_sms_booking_reminder'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Job completed'), 'email_code' => 'service_email_job_complete', 'sms_code' => 'service_sms_job_complete'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Rating/review left about you'), 'email_code' => 'service_email_rating_submit', 'sms_code' => 'service_sms_rating_submit'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Job cancelled'), 'email_code' => 'service_email_job_cancel', 'sms_code' => 'service_sms_job_cancel'))?>

	<div class="row-fluid notification_title">
		<div class="span8"><h4><?=__('Provide Services Notifications')?></h4></div>
		<div class="span4">
			<ul class="block-grid grid-span2 hidden-phone">
				<li><?=__('Email', true)?></li>
				<li><?=__('SMS', true)?></li>
			</ul>
		</div>
	</div>

	<?=$this->display_partial('account:notification_block', array('title' => __('Service request received'), 'email_code' => 'service_email_request_submit'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Question about your request'), 'email_code' => 'service_email_request_question', 'sms_code' => 'service_sms_request_question'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Price quote on your request'), 'email_code' => 'service_email_request_quote'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Quoting period ended'), 'email_code' => 'service_email_request_expired', 'sms_code' => 'service_sms_request_expired'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('12 hours remaining to select Provider'), 'email_code' => 'service_email_request_reminder'))?>

	<div class="row-fluid notification_title">
		<div class="span8"><h4><?=__('Request a Provider Notifications')?></h4></div>
		<div class="span4">
			<ul class="block-grid grid-span2 hidden-phone">
				<li><?=__('Email', true)?></li>
				<li><?=__('SMS', true)?></li>
			</ul>
		</div>
	</div>

	<?=$this->display_partial('account:notification_block', array('title' => __('New job offer'), 'email_code' => 'service_email_job_offer', 'sms_code' => 'service_sms_job_offer'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __('Answer to your question'), 'email_code' => 'service_email_job_answer', 'sms_code' => 'service_sms_job_answer'))?>
	<?=$this->display_partial('account:notification_block', array('title' => __("Answer to another Provider's question"), 'email_code' => 'service_email_job_answer_other', 'sms_code' => 'service_sms_job_answer_other'))?>

	<div class="row-fluid notification_title">
		<div class="span8"><h4><?=__('Stop All Notifications')?></h4></div>
		<div class="span4">
			<ul class="block-grid grid-span2 hidden-phone">
				<li><?=__('Email', true)?></li>
				<li><?=__('SMS', true)?></li>
			</ul>
		</div>
	</div>

	<?=$this->display_partial('account:notification_block', array('title' => __('Block all notitications'), 'email_code' => 'email_block_email', 'sms_code' => 'sms_block_sms'))?>

	<div class="row-fluid">
		<div class="span8">&nbsp;</div>
		<div class="span4 align-center">
			<?=form_submit('ask', __('Save changes'), 'class="btn btn-primary"')?>
		</div>
	</div>

<?=form_close()?>