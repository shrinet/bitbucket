<? if ($messages): ?>

	<ul class="breadcrumb">
		<li><a href="<?=root_url('messages')?>"><?=__('My Messages')?></a> <i class="icon-chevron-right divider"></i></li>
		<li class="active"><?=$this->page->title_name?></li>
	</ul>

	<div class="page-header">
		<h1><?=$this->page->title_name?></h1>
		<? if ($related_request): ?><h4 class="subheader"><?=__('Related to %s request', '<a href="'.$related_request->get_url('job').'">'.$related_request->title.'</a>')?></h4><? endif ?>
	</div>

	<div id="p_messages_thread">
		<?=$this->display_partial('messages:thread', array('messages'=>$messages))?>
	</div>

	<div class="row-fluid">
		<div class="span10 offset2">
			<?=form_open(array('id'=>'form_message_reply'))?>
				<input type="hidden" name="Message[thread_id]" value="<?= $message->message_thread_id ?>" />
				
				<div id="p_messages_reply_form"><?=$this->display_partial('messages:reply_form')?></div>

				<div class="form-actions align-right">
					<?=form_submit('submit', __('Send message'), 'id="button_reply" class="btn btn-primary btn-large"')?>
				</div>
			<?=form_close()?>
		</div>
	</div>

<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that message could not be found')))?>
<? endif ?>