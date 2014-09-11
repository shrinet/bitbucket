<? if (!$quote): ?>
<?
	$greeting_array = array(
		__('Dear %s', $request->user->username) => __('Dear %s', $request->user->username),
		__('Hello %s', $request->user->username) => __('Hello %s', $request->user->username),
		__('Hi %s', $request->user->username) => __('Hi %s', $request->user->username)
	);
	$closer_array = array(
		__('today') => __('today'),
		__('tomorrow') => __('tomorrow'),
		__('in a few days') => __('in a few days'),
		__('next week') => __('next week'),
		__('in two weeks') => __('in two weeks')
	);
	$parting_array = array(
		__('Thanks') => __('Thanks')
	);
?>
<div class="message_greeting">
	<?=form_dropdown('message_greeting', $greeting_array)?>,
</div>

<div class="control-group message_description">
	<div class="controls">	
		<textarea name="message_description" class="small span12" placeholder="<?= __('Use this as an opportunity to sell your services. Tell the customer some reasons they should pick you.') ?>"></textarea>
		<!--[ ] Save this personal message as default for my future quotes-->
	</div>
</div>

<div class="message_closer">
<? if ($type=="onsite"): ?>
	<?=__("I could visit onsite as soon as %s. If you'd like a price quote, click 'Set Appointment' on %s so we can share contact details and schedule a time.",
		array(form_dropdown('Quote[message_start]', $closer_array), c('site_name'))
	)?>
<? else: ?>
	<?=__("I could start %s. If you'd like to move forward, click 'Set Appointment' on %s so we can share contact details and schedule a time.",
		array(form_dropdown('Quote[message_start]', $closer_array), c('site_name'))
	)?>
<? endif ?>
</div>
<div class="message_parting">
<?=form_dropdown('message_parting', $parting_array)?>, <br />
<? if ($provider): ?><?=$provider->business_name?>
<? elseif ($this->user): ?><?=$this->user->name ?>
<? else: ?><?=__('Your name')?>
<? endif ?>
</div>

<textarea name="Quote[comment]" class="final_comment use_message_builder" style="display:none"></textarea>

<script>

Page.jobQuoteMessageFormFields = $.phpr.form().defineFields(function() {
	this.defineField('message_description').required("<?=__('Please enter a message')?>");
});

</script>

<? else: ?>
	<textarea name="Quote[comment]" class="span12 large final_comment"><?= $quote->comment ?></textarea>
<? endif ?>