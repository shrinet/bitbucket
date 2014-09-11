<?
	$providers = $providers->find_all();
?>
<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>
<div class="pagination">
	<ul>
		<? foreach (range('a', 'z') as $letter): ?>
		<li<?=$letter==$current_letter?' class="current"':''?>><a href="<?=root_url('directory/l/'.$letter)?>" onclick="return Page.selectLetter('<?=$letter?>')"><?=strtoupper($letter)?></a></li>
		<? endforeach ?>
	</ul>
</div>
<div class="row-fluid">
	<div class="span6 columns">
		<? foreach ($providers as $index=>$provider): ?>
			<? if (!($index & 1)):?>
				<div class="business_name"><a href="<?=$provider->get_url('profile')?>"><?=$provider->business_name?> - <?=$provider->role_name?></a></div>
			<? endif ?>
		<? endforeach ?>
	</div>
	<div class="span6 columns">
		<? foreach ($providers as $index=>$provider): ?>
			<? if ($index & 1):?>
				<div class="business_name"><a href="<?=$provider->get_url('profile')?>"><?=$provider->business_name?> - <?=$provider->role_name?></a></div>
			<? endif ?>
		<? endforeach ?>
	</div>
</div>