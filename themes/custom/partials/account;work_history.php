<div class="offer-request-control">
	<?=form_open(array('id'=>'form_work_history'))?>
		<input type="hidden" name="mode" value="<?= $mode ?>" />
		<input type="hidden" name="submode" value="<?= $submode ?>" />
		<input type="hidden" name="page" value="<?= $page ?>" />

		<? if ($this->user->is_provider && $this->user->is_requestor): ?>
			<ul class="nav nav-tabs" id="mode_filter">
				<li class="disabled"><a href="javascript:;"><?=__('Filter', true)?>:</a></li>
				<li class="<?=$mode=='offers'?'active':''?>"><a href="javascript:;" data-mode="offers"><?=__('Jobs Offered')?></a></li>
				<li class="<?=$mode=='requests'?'active':''?>"><a href="javascript:;" data-mode="requests"><?=__('Services Requested')?></a></li>
			</ul> 
		<? endif ?>
		<? if ($mode=="offers"): ?>
			<ul class="nav nav-pills" id="submode_filter_offers">
				<li class="<?=$submode=='open'?'active':''?>"><a href="javascript:;" data-submode="open"><?=__('Open')?></a></li>
				<li class="<?=$submode=='performed'?'active':''?>"><a href="javascript:;" data-submode="performed"><?=__('Performed')?></a></li>
				<li class="<?=$submode=='lost'?'active':''?>"><a href="javascript:;" data-submode="lost"><?=__('Lost')?></a></li>
				<li class="<?=$submode=='ignored'?'active':''?>"><a href="javascript:;" data-submode="ignored"><?=__('Ignored')?></a></li>
				<li class="<?=$submode=='cancelled'?'active':''?>"><a href="javascript:;" data-submode="cancelled"><?=__('Cancelled')?></a></li>
			</ul>
		<? endif ?>

		<? if ($mode=="offers"): ?>

			<? if ($activity): ?>
				<div class="row-fluid titles hide-for-small">
					<div class="span8"><strong><?=__('Job',true)?></strong></div>
					<div class="span4"><strong><?=__('Status',true)?></strong></div>
				</div>

				<? foreach ($activity as $index=>$job): ?>
					<? if ($index != 0): ?><hr /><? endif ?>
					<?=$this->display_partial('dash:job_panel', array('job'=>$job))?>
				<? endforeach ?>
			<? else: ?>
				<div class="well align-center">
					<?=__('No job offers found')?>
				</div>
			<? endif ?>

		<? else: ?>

			<? if ($activity): ?>
				<? foreach ($activity as $index=>$request): ?>
					<? if ($index != 0): ?><hr /><? endif ?>
					<?=$this->display_partial('dash:request_panel', array('request'=>$request))?>
				<? endforeach ?>
			<? else: ?>
				<p><?=__('No service requests found')?></p>
			<? endif ?>

		<? endif ?>

		<? if ($pagination): ?>
			<hr />
			<div id="p_site_pagination"><? $this->display_partial('site:pagination', array('pagination'=>$pagination)); ?></div>
		<? endif?>
	<?=form_close()?>
</div>