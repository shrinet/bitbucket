<? foreach ($messages as $message): ?>
<div class="message">
	<div class="row-fluid">
		<div class="span2 columns mobile-span3 align-right">
			<img src="<?=Bluebell_User::avatar($message->from_user)?>" alt="" />
		</div>
		<div class="span10 columns mobile-span9">
			<div class="row-fluid">
				<div class="span6 columns mobile-span2">
					<div class="username"><?=$message->from_user->username?></div>
				</div>
				<div class="span6 columns mobile-span2">
					<div class="date"><?=__('%s ago', Phpr_DateTime::interval_to_now($message->sent_at), true)?></div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="message_content"><?=$message->message_html?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<? endforeach ?>