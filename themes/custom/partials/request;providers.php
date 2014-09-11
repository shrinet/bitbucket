<div class="provider_list">
	<? foreach ($providers as $provider): ?>
	<div class="provider">
		<div class="row-fluid">
			<div class="span6">
				<div class="badge-control">
					<?=$this->display_partial('control:badge', array('provider'=>$provider, 'badge_mode'=>'detailed'))?>
				</div>
			</div>
			<div class="span6">
				<? if ($provider->ratings->count): ?>
				<? $rating = $provider->ratings->first(); ?>
				<p>
					<strong><?=__('Recent job for a %s', $rating->request_title)?>:</strong><br />
						<?=$rating->comment?>
				</p>
					<? else: ?>
					<!--p><?=__('This provider has not been rated yet')?></p-->
					<? endif ?>
			</div>
		</div>
	</div>
	<? endforeach ?>
</div>