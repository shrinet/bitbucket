<? if ($payment_type): ?>
	<? if ($payment_type->has_payment_form()): ?>
		<div class="payment_form">
			<? $payment_type->display_payment_form($this) ?>
		</div>
	<? else: ?>
		<p><?=__('Payment method %s has no payment form. Please pay and notify us of your action', $payment_type->name)?></p>
		<ul class="disc">
			<li><a href="<?=root_url()?>"><?=__('Return to the homepage')?></a></li>
		</ul>
	<? endif ?>
<? endif ?>