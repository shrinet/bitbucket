<?
	$new_messages = User_Message::check_new_messages($this->user);
?>
<div class="well">
	<h5><?=__('Messages',true)?></h5>
	<ul class="square">
		<li>
			<a href="<?=root_url('messages')?>">
				<? if ($new_messages > 0): ?>
					<?=__('You have %s new message(s)', $new_messages, true)?>
				<? else: ?>
					<?=__('No new messages',true)?>
				<? endif ?>
			</a>
		</li>
	</ul>
</div>