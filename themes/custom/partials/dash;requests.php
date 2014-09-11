<? foreach ($requests as $index=>$request): ?>
	<? if ($index != 0): ?><hr /><? endif ?>
	<?=$this->display_partial('dash:request_panel', array('request'=>$request))?>
<? endforeach ?>