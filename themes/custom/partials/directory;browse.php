
<?=$this->display_partial('directory:breadcrumb')?>

<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>

<div class="row">
	<div class="span3">
		<? foreach ($categories as $key=>$category): ?>
			<? if (($key) % 4 != 0) continue; // 1/4 ?>
			<h4><a href="<?=root_url('directory/b/'.$category->url_name)?>"><?=$category->name?></a></h4>
			<ul>
				<? foreach ($category->list_children() as $subcategory): ?>
					<li><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$subcategory->url_name)?>"><?=$subcategory->name?></a>
				<? endforeach ?>
			</ul>
		<? endforeach ?>
	</div>
	<div class="span3">
		<? foreach ($categories as $key=>$category): ?>
			<? if (($key+1) % 4 != 0) continue; // 2/4 ?>
			<h4><a href="<?=root_url('directory/b/'.$category->url_name)?>"><?=$category->name?></a></h4>
			<ul>
				<? foreach ($category->list_children() as $subcategory): ?>
					<li><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$subcategory->url_name)?>"><?=$subcategory->name?></a>
				<? endforeach ?>
			</ul>
		<? endforeach ?>
	</div>
	<div class="span3">
		<? foreach ($categories as $key=>$category): ?>
			<? if (($key+2) % 4 != 0) continue; // 3/4 ?>
			<h4><a href="<?=root_url('directory/b/'.$category->url_name)?>"><?=$category->name?></a></h4>
			<ul>
				<? foreach ($category->list_children() as $subcategory): ?>
					<li><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$subcategory->url_name)?>"><?=$subcategory->name?></a>
				<? endforeach ?>
			</ul>
		<? endforeach ?>        
	</div>
	<div class="span3">
		<? foreach ($categories as $key=>$category): ?>
			<? if (($key+3) % 4 != 0) continue; // 4/4 ?>
			<h4><a href="<?=root_url('directory/b/'.$category->url_name)?>"><?=$category->name?></a></h4>
			<ul>
				<? foreach ($category->list_children() as $subcategory): ?>
					<li><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$subcategory->url_name)?>"><?=$subcategory->name?></a>
				<? endforeach ?>
			</ul>
		<? endforeach ?>           
	</div>
</div>