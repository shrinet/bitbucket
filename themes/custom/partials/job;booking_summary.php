<div class="row-fluid">
	<div class="span8">

		<? if (!$is_cancelled): ?>
			<div id="p_job_booking_time"><?=$this->display_partial('job:booking_time', array('quote'=>$quote))?></div>
		<? else: ?>
			<div class="row-fluid">
				<div class="span4"><p class="detail_label"><?=__('Start time')?></p></div>
				<div class="span8">
					<p class="detail_text">
						<?=__('Booking cancelled')?>
					</p>
				</div>
			</div>
		<? endif ?>

		<div class="row-fluid">
			<div class="span4"><p class="detail_label"><?=__('Contact info')?></p></div>
			<div class="span8">
				<p class="detail_text">
					<?=$provider->business_name?><br />
					<?=$provider->phone?> <?=($provider->phone&&$provider->mobile)?'/':'' ?> <?=$provider->mobile?><br />
					<?=mailto_encode($provider->user->email)?>
				</p>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span4"><p class="detail_label"><?=__('Price quoted')?></p></div>
			<div class="span8">
				<p class="detail_price">
					<?=format_currency($quote->price)?>
					<span class="detail_price_summary">(<?=Bluebell_Quote::price_summary($quote)?>)</span>
					<? if (Bluebell_Quote::price_terms($quote)): ?>
						<span class="detail_price_terms">* <?=Bluebell_Quote::price_terms($quote)?></span>
					<? endif ?>
				</p>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span4"><p class="detail_label"><?=__('Personal Note')?></p></div>
			<div class="span8"><p class="detail_text"><?=Phpr_String::show_more_link($quote->comment, 150, __('Show more', true))?></p></div>
		</div>

	</div>
	<div class="span4">
		<div class="map" id="map_booking_address"><?=__('Loading map...')?></div>
		<div class="address" id="booking_address"><?=$request->user->address_string?></div>
	</div>    
</div>
