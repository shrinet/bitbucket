<div class="row-fluid job">
	<div class="span8">
		<div class="title"><a href="<?=$job->get_url('job')?>"><?=$job->title?></a></div>
		<div class="info"><?=Bluebell_Request::required_by($job)?> | <?=Bluebell_Request::location($job)?></div>
		<div class="description"><?=$job->description_summary?></div>
	</div>
	<div class="span4">
		<div class="status">
			<div class="time">
				<? if ($job->status_code==Service_Status::status_active): ?>
					<?=__('Quoting closes in')?>
					<span><?=$job->get_remaining_time(true)?></span>
				<? endif ?>
			</div>
			<? if ($job->status_code==Service_Status::status_cancelled): ?>
				<div><?=__('Job cancelled')?></div>
			<? elseif ($job->status_code==Service_Status::status_active): ?>
				<div><a href="<?=$job->get_url('job')?>" class="btn btn-primary btn-small"><?=__('View Details')?></a></div>
			<? else: ?>
				<div><?=__('Quoting closed')?></div>
			<? endif ?>
		</div>
	</div>
</div>