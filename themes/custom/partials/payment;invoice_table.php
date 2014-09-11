<table class="table table-striped table-invoice">
	<thead>
		<tr>
			<th class="item-description"><?=__('Item')?></th>
			<th class="numeric"><?=__('Price')?></th>
			<th class="numeric"><?=__('Discount')?></th>
			<th class="numeric"><?=__('Tax')?></th>
			<th class="numeric last"><?=__('Total')?></th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($invoice->items as $index=>$item): ?>
		<tr>
			<td>
				<div class="product_description">
					<?=$item->quantity?>x <?=$item->description?>
				</div>
			</td>
			<td class="numeric"><?=format_currency($item->price)?></td>
			<td class="numeric"><?=format_currency($item->discount)?></td>
			<td class="numeric"><?=format_currency($item->tax)?></td>
			<td class="numeric last total"><?=format_currency($item->total)?></td>
		</tr>
		<? endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" class="align-right"><?=__('Subtotal')?></td>
			<td class="numeric total"><?=format_currency($invoice->subtotal) ?></td>
		</tr>
		<? foreach ($invoice->list_item_taxes() as $tax): ?>
			<tr>
				<td class="numeric" colspan="4"><?=__('Sales tax')?> (<?=($tax->name) ?>)</td>
				<td class="numeric total"><?=format_currency($tax->total) ?></td>
			</tr>
		<? endforeach ?>
		<tr class="grand-total">
			<td class="blank">&nbsp;</td>
			<td class="numeric" colspan="3"><?=__('Total')?></td>
			<td class="numeric"><span class="product_price"><?=format_currency($invoice->total) ?></span></td>
		</tr>
	</tfoot>
</table>​