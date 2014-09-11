<?=form_open(array('id' => 'request_form', 'class'=>''))?>

	<input type="hidden" name="redirect" value="<?=root_url('request/review')?>" />
	<input type="hidden" name="redirect_assist" value="<?=root_url('request/assist')?>" />

	<div class="page-header">
		<h1><?=$this->page->title_name?></h1>
		<h4 class="subheader"><?=__('Submit your request to our pool of service providers')?></h4>
	</div>
	<div class="row-fluid">
		<div class="span8">
			<div id="p_request_form">
				<?=$this->display_partial('request:request_form')?>
			</div>
			<div class="form-actions">
				<button name="submit" type="submit" class="btn btn-large btn-primary btn-icon">
					<?=__('Submit Request')?> <i class="icon-chevron-right"></i>
				</button>
			</div>
		</div>
		
		<div class="span4">
			<div class="well hide-for-small">
				<?=content_block('request_right_panel', 'Request Right Panel')?>
			</div>
		</div>
	</div>
<?=form_close()?>
