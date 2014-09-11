<div class="row-fluid request">
	<div class="span7 columns">
		<div class="title"><a href="<?=$request->get_url('request/manage')?>"><?=__('%s Request', $request->title)?></a></div>
		<div class="info"><?=Bluebell_Request::required_by($request)?> | <?=Bluebell_Request::location($request)?></div>
		<div class="description"><?=$request->description_summary?></div>
	</div>
	<div class="span5 columns">
		<ul class="block-grid grid-span2">
			<li class="time">
				<? if ($request->status_code == Service_Status::status_active): ?>
					<span><?=$request->get_remaining_time(true)?></span>
					<?=__('to get quotes')?>
				<? endif ?>
			</li>
			<li class="status">
				<? 
					$show_button = true; 
				?>
				<p>
					<? if ($request->status_code == Service_Status::status_active): ?>
						<span class="quotes">
							<a href="<?=$request->get_url('request/manage')?>#quotes"><?=$request->total_quotes?></a>
						</span> 
						<span>
							<?=__('quote received')?>
						</span>

						<a href="<?=$request->get_url('request/manage')?>" class="btn btn-small btn-primary">
							<?=__('View Details')?>
						</a>
						<br>
						<a href="<?=$request->get_url('request/providers')?>" class="btn btn-small btn-primary">
							<?=__("Search Pro's")?>
						</a>

					<? elseif ($request->status_code == Service_Status::status_cancelled): ?>
						<span>
							<?=__('Request cancelled by consumer')?>
						</span>
					
					<? elseif ($request->status_code == Service_Status::status_expired): ?>
						<span class="label label-warning">
							<?=__('Quote period has ended')?>
						</span>
					
					<? elseif ($request->status_code == Bluebell_Request::status_booked): ?>
						<span class="label label-warning">
							<i class="icon-calendar-empty"></i> <?=__('Job scheduled')?>
						</span>

						<a href="<?=$request->get_url('request/manage')?>" class="btn btn-small">
							<?=__('View Booking')?>
						</a>

					<? elseif ($request->status_code == Service_Status::status_closed || $request->status_code == Service_Status::status_archived): ?>
						<span class="label label-success">
							<i class="icon-ok"></i> <?=__('Job completed')?>
						</span>

						<a href="<?=$request->get_url('request/manage')?>" class="btn btn-small">
							<?=__('View Booking')?>
						</a>
					
					<? endif ?>
				</p>
			</li>
		</ul>
	</div>
</div>
