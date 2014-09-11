<div class="row-fluid">
	<div class="span4 quote_list">
		<ul>
			<? foreach ($request->quotes as $quote): ?>
			<? if (!$quote->provider) continue; ?>
				<li id="quote_list_<?=$quote->id?>" data-quote-id="<?=$quote->id?>">
					<div class="quote_item">
						<? if ($quote->quote_type == Bluebell_Quote::quote_type_onsite): ?>
							<div class="consult"><?=__("Consult req'd")?></div>
						<? else: ?>
							<div class="price"><?=format_currency($quote->price)?><span>00</span></div>
						<? endif ?>
						<div class="username"><a href="javascript:;"><?=$quote->provider->business_name?></a></div>
						<div class="icons">
							<? if ($quote->comment_html): ?><span class="note">Note</span><? endif ?>
							<? if ($quote->messages->count > 0): ?><span class="chat">Chat</span><? endif ?>
						</div>
					</div>
				</li>

			<? endforeach ?>
		</ul>
	</div>
	<div class="span8 quote_panel">

		<? foreach ($request->quotes as $quote): ?>
		<? if (!$quote->provider) continue; ?>
			<div id="p_quote_panel_<?=$quote->id?>" class="quote" data-quote-id="<?=$quote->id?>" style="display:none">
				<?=$this->display_partial('request:quote_panel', array('quote'=>$quote))?>
			</div>
		<? endforeach ?>

	</div>
</div>
