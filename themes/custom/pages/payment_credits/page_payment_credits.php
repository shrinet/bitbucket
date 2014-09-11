<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>
<?=form_open(array('id'=>'form_credits', 'class'=>''))?>
	<input type="hidden" name="redirect" value="<?=root_url('payment/pay/%s')?>" />
	<input type="hidden" name="item_name" value="<?=__('%s Credits', c('site_name'))?>" />
	<table class="table">
		<thead>
			<tr>
				<th><?=__('Credits')?></th>
				<th><?=__('Price')?></th>
			</tr>
		</thead>
		<tbody>
			<? foreach (Payment_Credits::get_table() as $table): ?>
			<tr>
				<td>
					<label class="radio">
						<input type="radio" name="credits" value="<?=$table->credit?>" />
						<?= $table->credit ?>
					</label>
				</td>
				<td>
					<?=format_currency($table->cost)?> 
					<?=__('(%s/credit)', format_currency($table->cost/$table->credit))?>
				</td>
			</tr>
			<? endforeach ?>
		</tbody>
	</table>
	<div class="form-actions">
		<input type="submit" class="btn btn-success btn-large" value="<?=__('Purchase Credits')?>" />
	</div>
<?=form_close()?>
<script>
	$.phpr.form().validate('#form_credits').action('payment:on_buy_credits');
</script>