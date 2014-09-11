<div class="control-group">
	<label class="control-label"><?=__('Your Message')?></label>
	<div class="controls">
		<textarea name="Message[message]" id="message_reply_message" class="span12"></textarea>
	</div>
</div>

<script>

Page.messagesReplyFormFields = $.phpr.form().defineFields(function(){
	this.defineField('Message[message]', 'Message').required("<?=__('Please enter a reply to this message')?>");
});

</script>