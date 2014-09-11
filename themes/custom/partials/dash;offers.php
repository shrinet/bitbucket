<?
	$filter = (isset($filter)) ? $filter : 'active';
?>
<div class="row-fluid">
	<div class="span6">
		<ul class="nav nav-pills offer-filter">
			<li class="disabled"><a href="javascript:;"><?=__('Filter', true)?>:</a></li>
			<li class="<?=($filter=="active")?'active':''?>"><a href="javascript:;" data-filter-type="active"><?=__('Offers')?></a></li>
			<li class="<?=($filter=="booked")?'active':''?>"><a href="javascript:;" data-filter-type="booked"><?=__('Won')?></a></li>
			<li class="<?=($filter=="ended")?'active':''?>"><a href="javascript:;" data-filter-type="ended"><?=__('Lost')?></a></li>
		</ul>
	</div>
	<div class="span6">
		<? if ($request_max_bids = c('request_max_bids', 'service')): ?>
			<div class="request_max_bids"><span class="secondary label radius"><?=__('Quoting closes when a job gets %s quotes', $request_max_bids)?></span></div>
		<? endif ?>
	</div>
</div>

<div class="row-fluid titles hide-for-small">
	<div class="span8"><strong><?=__('Job',true)?></strong></div>
	<div class="span4 align-center"><strong><?=__('Status',true)?></strong></div>
</div>

<? foreach ($jobs as $index=>$job): ?>
	<? if ($index != 0): ?><hr /><? endif ?>
	<?=$this->display_partial('dash:job_panel', array('job'=>$job))?>
<? endforeach ?>