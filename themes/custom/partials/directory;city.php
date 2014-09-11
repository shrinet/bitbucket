
<?=$this->display_partial('directory:breadcrumb')?>

<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>
<div class="row-fluid">
	<div class="span8">

		<? if ($city): ?>
			<div id="p_directory_search_form">
				<?=$this->display_partial('directory:search_form', array('placeholder'=>__('Search for providers in %s. Eg: Plumber, Gardener, etc.', $city->name . ', ' . $state->code)))?>
			</div>
			<hr />
		<? endif ?>


		<? if ($roles): ?>
			<h4><?=__('Browse roles')?></h4>
			<ul class="block-grid grid-span3">
				<? foreach ($roles as $role): ?>
				<?
					$link = strtolower(root_url('directory/a/'.$country->code.'/'.$state->code.'/'.$city->url_name.'/'.Phpr_Inflector::slugify($role->name).'/'.$role->id.'/top'));
				?>
					<li><a href="<?=$link?>"><?=$role->name?></a></li>
				<? endforeach ?>
			</ul>
		<? else: ?>
			<?=global_content_block('directory_not_found', 'Directory no providers found')?>
		<? endif ?>
	</div>
	<div class="span4">
		<div class="well">
			<div id="p_directory_request_panel"><?=$this->display_partial('directory:request_panel')?></div>
		</div>
	</div>
</div>