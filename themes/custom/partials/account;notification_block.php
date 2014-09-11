<?
	$sms_code = (isset($sms_code)) ? $sms_code : null;
	$email_code = (isset($email_code)) ? $email_code : null;
?>
<div class="row-fluid notification_block">
	<div class="span8 columns"><p><?=$title?></p></div>
	<div class="span4 columns">
		<ul class="block-grid grid-span2">
			<? if ($email_code): ?><li><label class="checkbox"><span class="visible-phone"><?=__('Email', true)?></span> <?=form_checkbox('User['.$email_code.']', true, $this->user->$email_code)?></label></li><? endif ?>
			<? if ($sms_code): ?><li><label class="checkbox"><span class="visible-phone"><?=__('SMS', true)?></span> <?=form_checkbox('User['.$sms_code.']', true, $this->user->$sms_code)?></label></li><? endif ?>
		</ul>
	</div>
</div>