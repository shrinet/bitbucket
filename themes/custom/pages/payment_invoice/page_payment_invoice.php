<? if ($invoice): ?>
	<div class="page-header">
		<h1><?=$this->page->title_name?></h1>
		<h4 class="subheader"><?=$this->page->description?></h4>
	</div>

	<!-- Invoice header -->
	<div class="row-fluid">
		<div class="span6">

			<h4><?=__('Bill to')?></h4>

			<p>
				<?= $invoice->billing_name ?><br />
				<? if ($invoice->billing_street_addr): ?><?= $invoice->billing_street_addr ?><br /><? endif ?>
				<? if ($invoice->billing_city||$invoice->billing_zip): ?><?= $invoice->billing_city ?> <?= $invoice->billing_zip ?><br /><? endif ?>
				<? if ($invoice->billing_state): ?><?= $invoice->billing_state->name ?>, <? endif ?>
				<? if ($invoice->billing_country): ?><?= $invoice->billing_country->name ?><? endif ?>
				<? if ($invoice->billing_phone): ?><br /><?= $invoice->billing_phone ?><? endif ?>
			</p>
		</div>
		<div class="span6 align-right">
			<h2><?=__('Invoice %s', $invoice->id)?></h2>
			<p><?=__('Date: %s', $invoice->sent_at->to_long_date_format())?></p>
		</div>
	</div>

	<!-- Invoice table -->
	<div id="p_payment_invoice"><? $this->display_partial('payment:invoice_table', array('invoice'=>$invoice)) ?></div>

	<!-- Status -->
	<? if ($invoice->payment_type): ?>
		<? if ($invoice->payment_type->has_payment_form() && !$invoice->is_payment_processed()): ?>
			<div class="align-right">
				<div class="alert alert-error"><i class="icon-exclamation-sign"></i> <?=__('This invoice has not been paid')?></div>
			</div>
			<div class="form-actions align-right">
				<a class="btn btn-success btn-large btn-icon" href="<?= root_url('payment/pay/'.$invoice->hash) ?>">
					<i class="icon-ok"></i> 
					<?=__('Pay this Invoice')?>
				</a>
			</div>
		<? else: ?>
			<div class="align-right">
				<div class="alert alert-success"><i class="icon-ok"></i> <?=__('This invoice has been paid!')?></div>
			</div>
			<div class="form-actions align-left">
				<p><a href="<?=root_url('/dashboard')?>">Back to Dashboard</a></p>
			</div>
			<div class="form-actions align-right">
				<p><?=__('Thank you for your business.')?></p>
			</div>
		<? endif ?>
	<? else: ?>
		<div class="alert alert-error"><?=__('Sorry there are no payment gateways available to use!')?></div>
	<? endif ?>

<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that invoice could not be found')))?>
<? endif ?>