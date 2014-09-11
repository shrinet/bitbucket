<? if ($request): ?>
	<?=form_open(array('id'=> 'assist_form'))?>

		<input type="hidden" name="redirect" value="<?=root_url('request/review')?>" />
		<input type="hidden" name="redirect_assist" value="<?=root_url('request/assist')?>" />
		<input type="hidden" name="Request[category_id]" value="" id="request_category_id" />
		<input type="hidden" name="Request[title]" value="" id="request_title" />
		
		<div class="page-header">
			<h1><?=__('Browse providers')?></h1>
			<h4 class="subheader"><?=__('Please select the service professional who can best meet your needs')?></h4>
		</div>
		<div class="row-fluid">
			<div class="span8">
				<div id="p_category_form"><?= $this->display_partial('request:category_form') ?></div>
				<div class="form-actions align-right">
					<?= form_submit('assist', __('Continue to Review'), 'class="btn btn-success btn-large"') ?>
				</div>
			</div>
		</div>
	<?=form_close()?>

	<div id="popup_suggest_category_success" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-body">
			<p class="lead"><?=__('Thanks! Your suggestion has been submitted. We will let you know the outcome via Email.')?></p>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="btn popup-close"><?=__('Close', true)?></a>
		</div>
	</div>

	<div id="popup_suggest_category" class="modal hide fade" tabindex="-1" role="dialog">
		<?=form_open(array('id'=> 'assist_suggest_form'))?>
			<div class="modal-header">
				<h3><?=__('Suggest a new service for %s', c('site_name'))?></h3>
			</div>
			<div class="modal-body">
				<?= $this->display_partial('request:suggest_form') ?>	    
			</div>
			<div class="modal-footer">
				<input type="submit" name="submit" value="<?=__('Submit suggestion')?>" class="btn btn-success" />
			</div>
		<?=form_close()?>
	</div>

<script>

Page.requestAssistFormFields = $.phpr.form().defineFields(function() {
	this.defineField('select_category').required();
	this.defineField('select_alpha').required();
});

</script>

<? else: ?>
	<?= $this->display_partial('site:404', array('error_message'=>__('Sorry, something went wrong with your request. Please check that your browser has cookies enabled or change your browser settings to default.'))) ?>
<? endif ?>