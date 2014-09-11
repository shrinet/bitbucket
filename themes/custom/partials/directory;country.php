
<?=$this->display_partial('directory:breadcrumb')?>

<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>
<div class="row-fluid">
	<div class="span8">

		<?=form_open(array('id'=>'form_directory_search'))?>
			<div id="p_directory_search_form">
				<?=$this->display_partial('directory:search_form', array('placeholder'=>__('Search for providers: Enter your address or zip/postal code')))?>
			</div>
		<?=form_close()?>

		<hr />

		<? if ($country): ?>
			<h5><?=__('Browse %s Areas', $country->code)?></h5>
			<ul class="block-grid grid-span3">
				<? foreach ($country->states as $state): ?>
					<li><a href="<?=strtolower(root_url('directory/a/'.$country->code.'/'.$state->code))?>"><strong><?=$state->code?></strong> - <?=$state->name?></a></li>
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