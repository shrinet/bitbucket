
<?=$this->display_partial('directory:breadcrumb')?>

<?
	$providers = $providers->find_all();

	if ($parent_mode == 'area')
		$link = strtolower(root_url('directory/a/'.$country->code.'/'.$state->code.'/'.$city->url_name.'/'.$role->url_name));
	else 
		$link = strtolower(root_url('directory/b/'.$category->url_name.'/'.$role->url_name));
?>
<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>

<div class="row">
	<div class="span9">
		<? if ($providers->count): ?>
			<ul class="nav nav-pills">
				<li class="disabled"><a href="javascript:;"><?=__('Filter', true)?>:</a></li>
				<li class="<?=($filter=='top')?'active':''?>"><a href="<?=$link.'/top'?>"><?=__('Top Rated')?></a></li>
				<li class="<?=($filter=='cheap')?'active':''?>"><a href="<?=$link.'/cheap'?>"><?=__('Cheapest')?></a></li>
				<li class="<?=($filter=='reliable')?'active':''?>"><a href="<?=$link.'/reliable'?>"><?=__('Most Reliable')?></a></li>
			</ul>

			<div class="provider_list">
				<? foreach ($providers as $provider): ?>
					<div class="provider">
						<div class="row-fluid">
							<div class="span6">
								<div class="badge-control">
									<?=$this->display_partial('control:badge', array('provider'=>$provider, 'badge_mode'=>'detailed'))?>
								</div>
							</div>
							<div class="span6">
								<? if ($provider->ratings->count): ?>
								<? $rating = $provider->ratings->first(); ?>
								<p>
									<strong><?=__('Recent job for a %s', $rating->request_title)?>:</strong><br />
									<?=$rating->comment?>
								</p>
								<? else: ?>
									<p><?=__('This provider has not been rated yet')?></p>
								<? endif ?>
							</div>
						</div>
					</div>
				<? endforeach ?>
			</div>
		<? else: ?>
			<?=global_content_block('directory_not_found', 'Directory no providers found')?>
		<? endif ?>
	</div>
	<div class="span3">
		<div class="well">
			<h4><?=__('Request a free quote from a %s', $role->name)?></h4>
			<p><?=__('Been searching high and low for the best %s to meet your needs? Look no further...', $role->name)?></p>
			<p><a href="javascript:;" id="button_request_popup" class="btn btn-primary btn-block"><?=__('Get a free quote!')?></a></p>
		</div>
	</div>
</div>

<div id="popup_request" class="modal hide fade" tabindex="-1" role="dialog">
	<?=form_open(array('id'=>'form_request'))?>
		<div class="modal-header">
			<h3><?=__('Get Quotes from a %s', $role->name)?></h3>
		</div>
		<div class="modal-body">
			<?=$this->display_partial('request:quick_form', array('role'=>$role))?>
		</div>
		<div class="modal-footer">
			<?=form_submit('submit', __('Send request'), 'class="btn btn-primary" id="button_submit_request"')?>
		</div>
	<?=form_close()?>
</div>

<div id="popup_request_success" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-body">
		<?=global_content_block('directory_request_submit', 'Directory request submitted')?>
	</div>
	<div class="modal-footer">
		<?=form_button('close', __('Close', true), 'class="btn popup-close"')?>
	</div>
</div>