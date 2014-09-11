<? if ($invoice): ?>
	<div class="page-header">
		<h1><?=$this->page->title_name?></h1>
		<h4 class="subheader"><?=$this->page->description?></h4>
	</div>

	<? if ($invoice->is_payment_processed()): ?>
		<h4 class="text-success">
			<span class="ring-badge ring-badge-success"><i class="icon-ok"></i></span> <?=__('This invoice has already been paid!')?>
		</h4>
		<p>View <a href="<?= $invoice->get_receipt_url() ?>">invoice <?=$invoice->id?></a></p>
	<? else: ?>

		<!-- Invoice header -->
		<div class="row-fluid">
			<div class="span6">
				<?=form_open(array('id'=>'form_invoice_details'))?>

					<h4><?=__('Bill to')?></h4>
					<div class="form-field name">
						<label><?=__('Your name')?></label>
						<div class="edit row-fluid">
							<div class="span6">
								<input type="text" name="Invoice[billing_first_name]" value="<?= form_value($invoice, 'billing_first_name') ?>" placeholder="<?=__('Your name')?>" id="invoice_billing_first_name" class="span12" />
							</div>
							<div class="span6">
								<input type="text" name="Invoice[billing_last_name]" value="<?= form_value($invoice, 'billing_last_name') ?>" placeholder="<?=__('Your surname')?>" id="invoice_billing_last_name" class="span12" />
							</div>
						</div>
					</div>        

					<div class="form-field address" id="profile_details_address">
						<label for="invoice_billing_address"><?=__('Business address')?></label>
						<div class="edit">
							<input type="text" name="Invoice[billing_street_addr]" value="<?= form_value($invoice, 'billing_street_addr') ?>" placeholder="<?= __('Business address') ?>" class="span12" id="invoice_billing_address" />
							<div class="row-fluid">
								<div class="span6">
									<input type="text" name="Invoice[billing_city]" value="<?= form_value($invoice, 'billing_city') ?>" placeholder="<?= __('City', true) ?>" class="span12" id="invoice_billing_city" />
								</div>
								<div class="span6">
									<input type="text" name="Invoice[billing_zip]" value="<?= form_value($invoice, 'billing_zip') ?>" placeholder="<?= __('Zip / Postal Code', true) ?>" class="span12" id="invoice_billing_zip" />
								</div>
							</div>
							<div class="row-fluid">
								<div class="span6">
									<?=form_dropdown('Invoice[billing_country_id]', Location_Country::get_name_list(), form_value($invoice, 'billing_country_id'), 'id="invoice_billing_country_id" class="span12"',  __('-- Select --', true))?>
								</div>
								<div class="span6">
									<?=form_dropdown('Invoice[billing_state_id]', Location_State::get_name_list(form_value($invoice, 'billing_country_id')), form_value($invoice, 'billing_state_id'), 'id="invoice_billing_state_id" class="span12"', __('-- Select --', true));?>
								</div>
							</div>
						</div>
					</div>

					<div class="form-field phone">
						<label for="provide_phone"><?= __('Phone number', true) ?></label>
						<div class="edit">
							<input type="text" name="Invoice[billing_phone]" value="<?= form_value($invoice, 'billing_phone') ?>" placeholder="<?= __('Phone number') ?>" class="span12" id="invoice_billing_phone" />
						</div>
					</div>                              

				<?=form_close()?>
			</div>
			<div class="span6 align-right">
				<h2><?=__('Invoice %s', $invoice->id)?></h2>
				<p><?=__('Date: %s', $invoice->sent_at->to_long_date_format())?></p>
			</div>
		</div>

		<!-- Invoice table -->
		<div id="p_payment_invoice"><? $this->display_partial('payment:invoice_table', array('invoice'=>$invoice)) ?></div>

		<!-- Payment method -->
		<h5><?=__('Please choose a payment method')?></h5>

		<?=form_open(array('class'=>''))?>
			<? 
				$payment_types = Payment_Type::list_applicable($invoice->country_id); 
			?>
			<? foreach ($payment_types as $type): ?>
				<label for="<?='type'.$type->id?>" class="radio">
					<input type="radio" <?=($type->id == $invoice->payment_type_id)?'checked="checked"':''?> 
						value="<?=$type->id?>" name="payment_type" id="<?='type'.$type->id?>" 
						onchange="$(this).phpr().post('payment:on_update_payment_type').update('#p_payment_form', 'payment:form').send()" />
					<?= h($type->name) ?>
				</label>
			<? endforeach ?>
		<?=form_close()?>

		<!-- Payment form -->
		<div id="p_payment_form"><?=$this->display_partial('payment:form') ?></div>

	<? endif ?>
<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that payment could not be found')))?>
<? endif ?>