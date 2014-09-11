<?
	$request = (isset($request)) ? $request : null;
?>
<? if (!$request): ?>
	<h4><?=__('Request a free quote!')?></h4>
	<?=form_open(array('id'=>'form_request_panel'))?>
		<input type="hidden" name="open_request" value="1" />
		<?=$this->display_partial('request:quick_form')?>
		<?=form_submit('submit', __('Send request'), 'class="btn btn-primary btn-block popup-close" id="submit_request_panel"')?>
	<?=form_close()?>
<? else: ?>
	<?=global_content_block('directory_request_submit', 'Directory request submitted')?>
<? endif ?>