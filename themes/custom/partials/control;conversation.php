<?
	$has_messages = false;
	$to_user_id = (isset($to_user_id)) ? $to_user_id : $quote->provider->user_id;
?>
<? foreach ($quote->messages as $index=>$message): ?>
	<? 
		$is_self = $message->from_user->id==$this->user->id; 
	?>
	<? if (!$has_messages): $has_messages = true; ?>
		<input type="hidden" name="Message[thread_id]" value="<?= $message->message_thread_id ?>" />
		<h4 class="conv_header"><?=__('Your conversation with %s', $quote->provider->business_name)?></h4>
	<? endif ?>

	<div class="conv_message <?=$is_self ? 'left' : 'right'?>">
		<div class="message">
			<p><?=Phpr_String::show_more_link($message->message, 250, __('Show more', true))?><p>
			<p class="time"><?=__('%s ago', Phpr_DateTime::interval_to_now($message->sent_at), true)?></p>
		</div>
		<div class="user">
			<span class="arrow"></span>
			<?=$is_self ? __('You', true) : $quote->provider->business_name?>
		</div>
	</div>
<? endforeach ?>


<div class="send_message">
	<div class="row-fluid">
		<div class="span10">
			<div class="control-group">
				<input type="text" name="Message[message]" value="" class="span12" placeholder="<?= __('Type your message or question and click Send') ?>" />
			</div>
		</div>
		<div class="span2">
			<?=form_submit('send', 'Send', 'class="send_message_button btn btn-block"')?>
		</div>
	</div>
</div>

<p class="terms"><?=__('Sharing your contact details is against our terms and conditions')?></p>

<input type="hidden" name="Message[to_user_id]" value="<?= $to_user_id ?>" />
<input type="hidden" name="Message[object_id]" value="<?= $quote->id ?>" />
<input type="hidden" name="Message[object_class]" value="<?= get_class($quote) ?>" />

<script>

Page.controlConversationFormFields = $.phpr.form().defineFields(function() {
	this.defineField('Message[message]').required("<?=__('Please specify a message',true)?>");
});

</script>
