<?
	$is_editable = (isset($is_editable)) ? $is_editable : true;
?>
<div class="row-fluid quote_title">
	<div class="span6"><h4><?=__('Quote Details')?></h4></div>
	<div class="span6 align-right">
		<? if ($is_editable): ?>
			<a href="javascript:;" id="link_edit_quote">
				<i class="icon-pencil"></i> <?=__('Modify quote')?>
			</a>
			 &nbsp; &nbsp; 
			<a href="javascript:;" id="link_delete_quote">
				<i class="icon-ban-circle"></i> 
				<?=__('Cancel quote')?>
			</a>
		<? endif ?>
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<?=$quote->comment_html?>
	</div>
	<div class="span6">
		
		<div id="p_control_badge" class="badge-control">
			<?=$this->display_partial('control:badge', array('provider'=>$provider))?>
		</div>

	</div>
</div>
<table class="table table-bordered quote_summary">
	<thead>
		<tr>
			<th class="details"><?=__('Quote Details')?></th>
			<th class="price"><?=__('Price')?></th>
		</tr>
	</thead>
	<tbody>
		<? if ($quote->flat_labor_price): ?>
		<tr>
			<td><em><?=__('Labor')?></em>: <?=$quote->flat_labor_description?></td>
			<td><?=format_currency($quote->flat_labor_price)?></td>
		</tr>
		<? endif ?>

		<? if ($quote->flat_items): ?>
			<? foreach (json_decode($quote->flat_items) as $item): ?>
			<tr>
				<td><?=$item->description?></td>
				<td><?=format_currency($item->price)?></td>
			</tr>
			<? endforeach ?>
		<? endif ?>

		<? if ($quote->onsite_price_start||$quote->onsite_price_end): ?>
		<tr>
			<td><?=__('Onsite visit is required to give a price quote. Estimated cost provided.')?></td>
			<td>
				<?=(!$quote->onsite_price_start||!$quote->onsite_price_end)
					? format_currency($quote->onsite_price_end + $quote->onsite_price_start)
					: format_currency($quote->onsite_price_start) 
					. ' - ' 
					. format_currency($quote->onsite_price_end)?>
			</td>
		</tr>
		<? endif ?>
		<? if ($quote->onsite_travel_required): ?>
		<tr>
			<td>
				<?=($quote->onsite_travel_waived)
					? __('Travel fee (waived if chosen)')
					: __('Travel fee') ?>
			</td>
			<td>
				<?=($quote->onsite_travel_price)
					? format_currency($quote->onsite_travel_price)
					: __('Free',true) ?>
			</td>
		</tr>
		<? endif ?>
	</tbody>
</table>

<? if ($quote->flat_labor_price): ?>
<div class="row-fluid">
	<div class="span5 offset7">
		<table class="table quote_summary">
			<tbody>
				<tr>
					<th class="total"><div class="align-right"><?=__('Total')?></div></th>
					<td class="price"><?=format_currency($quote->price)?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<? endif ?>
<?=form_open(array('id'=>'form_quote_summary'))?>
	<input type="hidden" name="request_id" value="<?= $request->id ?>" />
<?=form_close()?>