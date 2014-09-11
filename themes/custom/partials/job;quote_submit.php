<?
	$quote_type = ($quote&&$quote->quote_type=='onsite') ? 'onsite' : 'flat_rate';
	$config = Service_Config::create();
?>
<div class="tabbable">
	<ul class="nav nav-tabs" id="quote_submit_tabs">
		<li class="<?=($quote_type==Bluebell_Quote::quote_type_flat_rate)?'active':''?>">
			<a href="#flat_rate" data-toggle="tab"><?=__('Flat rate')?></a>
		</li>
		<? if (!$config->quote_hide_onsite): ?>
			<li class="<?=($quote_type==Bluebell_Quote::quote_type_onsite)?'active':''?>">
				<a href="#onsite_visit" data-toggle="tab"><?=__('Onsite visit required')?></a>
			</li>
		<? endif ?>
	</ul>

	<div class="tab-content">
		<div class="tab-pane <?=($quote_type==Bluebell_Quote::quote_type_flat_rate)?'active':''?>" id="flat_rate">
			<?=form_open(array('id'=>'form_quote_flat_rate'))?>
				<input type="hidden" name="request_id" value="<?= $request->id ?>" />
				<input type="hidden" name="Quote[quote_type]" value="<?= Bluebell_Quote::quote_type_flat_rate ?>" />

				<h4><?=__('Price')?></h4>
				<div id="p_quote_flat_rate"><?=$this->display_partial('job:quote_flat_rate_form')?></div>

				<hr />

				<h4><?=__('Personal Message', true)?></h4>
				<div id="p_personal_message_flat"><?=$this->display_partial('job:quote_message_form', array('type'=>Bluebell_Quote::quote_type_flat_rate))?></div>

				<div class="form-actions">
					<? if ($this->user && $provider): ?>
						<div class="align-center">
							<?=form_submit('review', __('Submit Quote'), 'class="btn btn-primary btn-large"')?>
						</div>
						<div class="final_note align-center">
							<?=__('If this customer books you for this job, we will send you their contact details and guarantee you the job for a charge of 25% your price for labor.')?>
						</div>
					<? else:?>
						<div class="align-center">
							<a href="<?=root_url('provide')?>"><?=__('Create a provider profile to quote on this job!')?></a>
						</div>
					<? endif ?>
				</div>

			<?=form_close()?>
		</div>
		<? if (!$config->quote_hide_onsite): ?>
			<div class="tab-pane <?=($quote_type==Bluebell_Quote::quote_type_onsite)?'active':''?>" id="onsite_visit">
				<?=form_open(array('id'=>'form_quote_onsite'))?>
					<input type="hidden" name="request_id" value="<?= $request->id ?>" />
					<input type="hidden" name="Quote[quote_type]" value="<?= Bluebell_Quote::quote_type_onsite ?>" />

					<h4><?=__('Price')?></h4>
					<div id="p_quote_onsite"><?=$this->display_partial('job:quote_onsite_form')?></div>

					<hr />

					<h4><?=__('Personal Message', true)?></h4>
					<div id="p_personal_message_onsite"><?=$this->display_partial('job:quote_message_form', array('type'=>Bluebell_Quote::quote_type_onsite))?></div>

					<div class="form-actions">
						<? if ($this->user && $provider): ?>
							<div class="align-center">
								<?=form_submit('review', __('Submit Quote'), 'class="btn btn-primary btn-large"')?>
							</div>
							<div class="final_note align-center">
								<?=__('If this customer books you for this job, we will send you their contact details and guarantee you can meet the customer for a flat rate of $34.99.')?>
							</div>
						<? else:?>
							<div class="align-center">
								<a href="<?=root_url('provide')?>"><?=__('Create a provider profile to quote on this job!')?></a>
							</div>
						<? endif ?>
					</div>

				<?=form_close()?>
			</div>
		<? endif ?>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {
		Page.initQuoteForms();
	});
</script>
