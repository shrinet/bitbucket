
<hr />
<h4><?=__('Invoices')?></h4>

<? if (!$invoices || !$invoices->count): ?>
	<p>You have no invoices yet</p>
<? else: ?>

	<table class="license_table table table-striped table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Status</th>
				<th class="align-right last">Total</th>
				<th class="last"></th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($invoices as $invoice): ?>
				<?
					$url = root_url('payment/invoice/'.$invoice->id);
				?>
				<tr>
					<td><a href="<?= $url ?>"><?= $invoice->id ?></a></td>
					<td><a href="<?= $url ?>"><?= $invoice->sent_at->format('%x') ?></a></td>
					<td><a href="<?= $url ?>"><strong><?= h($invoice->status->name) ?></strong> since <?= $invoice->status_updated_at->format('%x') ?></a></td>
					<td class="total align-right"><a href="<?= $url ?>"><?= format_currency($invoice->total) ?></a></td>
					<td class="align-right last"><a href="<?= $url ?>" class="btn btn-small btn-block"><i class="icon-search"></i> View</a></td>
				</tr>
			<? endforeach ?>
		</tbody>
	</table>

<? endif ?>
