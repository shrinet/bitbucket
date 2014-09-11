<?
	$config = Service_Config::create();
?>
<table class="table labor_table">
	<thead>
		<tr>
			<th><?=__('Description of work')?></th>
			<th><?=__('Price for labor')?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="line_item">
				<textarea name="Quote[flat_labor_description]" class="span12" placeholder="<?= __('Briefly describe what is and is not included in your labor price') ?>"><?= form_value($quote, 'flat_labor_description')?></textarea>
			</td>
			<td class="price">
				<span class="currency"><?=Core_Locale::currency_symbol()?></span>
				<input type="text" name="Quote[flat_labor_price]" value="<?= form_value($quote, 'flat_labor_price') ?>" class="input-mini" />
			</td>
		</tr>
	</tbody>
</table>

<? if (!$config->quote_hide_materials): ?>
	<table class="table item_table">
		<thead>
			<tr>
				<th><?=__('Item or material description')?></th>
				<th><?=__('Price for material')?></th>
			</tr>
		</thead>
		<tbody>
			<tr class="shell">
				<td class="line_item">
					<input type="text" name="Quote[flat_item_description][]" value="" class="span12" placeholder="<?= __('List any materials or supplies not included in labor price') ?>" />
				</td>
				<td class="price">
					<span class="currency"><?=Core_Locale::currency_symbol()?></span>
					<input type="text" name="Quote[flat_item_price][]" value="" class="input-mini" />
				</td>
			</tr>
		</tbody>
	</table>
	<div class="add_line_item"><a href="javascript:;" id="link_add_line_item" onclick="Page.clickFlatRateAddItem(); return false"><?=__('Add another line item')?></a></div>
	<textarea name="Quote[flat_items]" style="display:none" id="quote_flat_items"><?= form_value($quote, 'flat_items') ?></textarea>
<? endif ?>

<div class="row total_cost">
	<div class="span6 offset6 align-center">
		<ul>
			<li><span class="total_cost_label"><?=__('Total Cost')?></span></li>
			<li><span class="total_cost_value" id="total_cost_value"><?=$quote ? format_currency($quote->price) : format_currency(0)?></span></li>
		</ul>
	</div>
</div>

<script>

Page.jobQuoteFlatRateFormFields = $.phpr.form().defineFields(function() {
	this.defineField('Quote[flat_labor_price]').required("<?=__('Please enter your cost of labor (Min: %s)', format_currency($config->min_labor_price))?>").number("<?=__('Cost of labor entered must be a number')?>").min(<?=$config->min_labor_price?>,"<?=__('Minimum cost of labor must be %s', format_currency($config->min_labor_price))?>");
	<? if (!$config->quote_hide_materials && $config->quote_materials_required): ?>
		this.defineField('Quote[flat_item_price][]').required("<?=__('Please enter your cost of materials')?>").number("<?=__('Cost of materials entered must be a number')?>");
	<? endif ?>
});

</script>