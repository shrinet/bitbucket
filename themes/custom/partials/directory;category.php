
<?=$this->display_partial('directory:breadcrumb')?>

<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>

<? if (count($roles)): ?>
	<div class="row">
		<div class="span3">
			<? foreach ($roles as $key=>$role): ?>
				<? if (($key) % 4 != 0) continue; // 1/4 ?>
				<h5><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$role->url_name)?>"><?=$role->name?></a></h5>
			<? endforeach ?>
		</div>
		<div class="span3">
			<? foreach ($roles as $key=>$role): ?>
				<? if (($key+1) % 4 != 0) continue; // 2/4 ?>
				<h5><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$role->url_name)?>"><?=$role->name?></a></h5>
			<? endforeach ?>
		</div>
		<div class="span3">
			<? foreach ($roles as $key=>$role): ?>
				<? if (($key+2) % 4 != 0) continue; // 3/4 ?>
				<h5><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$role->url_name)?>"><?=$role->name?></a></h5>
			<? endforeach ?>
		</div>
		<div class="span3">
			<? foreach ($roles as $key=>$role): ?>
				<? if (($key+3) % 4 != 0) continue; // 4/4 ?>
				<h5><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$role->url_name)?>"><?=$role->name?></a></h5>
			<? endforeach ?>
		</div>
	</div>
<? else: ?>
	<?=global_content_block('directory_not_found', 'Directory no providers found')?>
<? endif ?>