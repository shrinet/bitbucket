<ul class="breadcrumb">
	<li><a href="<?=root_url('directory')?>"><?=__('Directory', true)?></a> <i class="icon-chevron-right divider"></i></li>
	
	<? // Browse ?>
	<? if ($parent_mode=='browse'): ?><li><a href="<?=root_url('directory/browse')?>"><?=__('Browse', true)?></a> <i class="icon-chevron-right divider"></i></li><? endif ?>
	<? if (isset($category->name)): ?><li><a href="<?=$dir_url.='/'.strtolower($category->url_name)?>"><?=$category->name?></a> <i class="icon-chevron-right divider"></i></li><? endif ?>

	<? // Location ?>
	<? if (isset($country->name)): ?><li><a href="<?=$dir_url.='/'.strtolower($country->code)?>"><?=$country->name?></a> <i class="icon-chevron-right divider"></i></li><? endif ?>
	<? if (isset($state->name)): ?><li><a href="<?=$dir_url.='/'.strtolower($state->code)?>"><?=$state->name?></a> <i class="icon-chevron-right divider"></i></li><? endif ?>
	<? if (isset($city->name)): ?><li><a href="<?=$dir_url.='/'.$city->url_name?>"><?=$city->name?></a> <i class="icon-chevron-right divider"></i></li><? endif ?>

	<? // Role ?>
	<? if (isset($role->name)): ?><li><a href="javascript:;"><?=$role->name?></a> <i class="icon-chevron-right divider"></i></li><? endif ?>
</ul>