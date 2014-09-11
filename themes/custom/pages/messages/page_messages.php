<div class="page-header">
	<div class="row-fluid">
		<div class="span7">
			<h1><?=$this->page->title_name?></h1>
		</div>
		<div class="span5">
			<div class="main-controls">
				<div class="row-fluid">
					<?=form_open(array('id'=>'form_message_search'))?>
						<div class="input-append pull-right">
							<input type="text" name="search" placeholder="<?= __('Search messages') ?>" />
							<?=form_submit('submit', __('Search'), 'class="btn"')?>
						</div>
					<?=form_close()?>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="p_message_panel" class="all-messages">
	<?=$this->display_partial('messages:message_panel', array('messages'=>$messages))?>
</div>
