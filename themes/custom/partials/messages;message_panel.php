<? foreach ($messages->find_all() as $message): ?>
	<div class="message <?=($message->is_new) ? 'unread' : ''?>">
		<div class="row-fluid">
			<div class="span9 mobile-span9">
				<div class="row-fluid message-link" data-url="<?=root_url('message/'.$message->id)?>">
					<div class="span2 mobile-span3">
						<div class="avatar align_right"><img src="<?=Bluebell_User::avatar($message->from_user)?>" alt="" /></div>
					</div>
					<div class="span10 mobile-span9">
						<div class="recipient"><?= $message->recipients_string ?></div>
						<div class="subject"><?= $message->message_summary ?></div>
					</div>
				</div>
			</div>
			<div class="span3 mobile-span3">
				<div class="controls">
					<span class="date"><?=__('%s ago', Phpr_DateTime::interval_to_now($message->sent_at), true)?></span>
					<a href="javascript:;" data-message-id="<?=$message->id?>" class="link-delete">
						<i class="icon-remove-sign"></i> 
						<?=__('Delete', true)?>
					</a>
				</div>
			</div>
		</div>
	</div>
<? endforeach ?>