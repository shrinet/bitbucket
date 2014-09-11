<?
	$can_edit = isset($can_edit) ? $can_edit : true;
?>
<div class="box-header">
	<h6 class="pull-left"><?=__('Request Status', true)?></h6>
	<div class="pull-right"><?=$request->status_name?></div>
</div>
<div class="box-content">
	<? if ($can_edit): ?>
		<ul>            
			<? if ($request->status_code == Service_Status::status_expired): ?>
				<li class="remaining"><?=__('Quoting period has ended')?>
			<? else: ?>
				<li class="remaining"><?=__('%s remain', $request->get_remaining_time())?>
			<? endif ?>
				<a href="javascript:;" id="link_extend_time" data-request-id="<?=$request->id?>" class="small"><?=__('Extend time')?></a>
			</li>
			<li class="quotes">
				<? if ($request->total_quotes > 0): ?>
					<a href="#quotes"><?=__('%s quote(s)', $request->total_quotes)?></a>
				<? else: ?>
					<?=__('%s quote(s)', $request->total_quotes)?>
				<? endif ?>
			</li>
			<li class="questions">
				<? if ($request->total_questions > 0): ?>
					<a href="#questions"><?=__('%s public question(s)', $request->total_questions)?></a>
				<? else: ?>
					<?=__('%s public question(s)', $request->total_questions)?>
				<? endif ?>
			</li>
		</ul>
	<? endif ?>
</div>