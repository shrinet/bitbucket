<ul class="breadcrumb">
	<li><a href="<?=root_url('dashboard')?>"><?=__('My Jobs')?></a>  <i class="icon-chevron-right divider"></i></li>
	<li class="current"><?=$this->page->title_name?></li>
</ul>	

<? if ($request): ?>
	<div class="page-header">
		<h1><?=__('%s Request from %s', array($request->title, $request->user->username), true)?></h1>
		<? if ($request->status_code!=Service_Status::status_active): ?>
			<h4 class="subheader"><?=__('Sorry, this request has closed for bidding')?></h4>
		<? elseif ($request_max_bids = c('request_max_bids', 'service')): ?>
			<h4 class="subheader"><?=__('Only the first %s quote(s) are accepted, so quote now!', $request_max_bids)?></h4>
		<? endif ?>
	</div>

	<div class="row-fluid">

		<div class="span4">
			<div class="well job-details">

				<?= $this->display_partial('job:details') ?>

				<div class="row-fluid">
					<div class="span4 mobile-span3"><p class="detail"><?=__('Time')?>:</p></div>
					<div class="span8 mobile-span9"><p><?=Bluebell_Request::required_by($request)?></p></div>
				</div>

				<div class="row-fluid">
					<div class="span4 mobile-span3"><p class="detail"><?=__('Location')?>:</p></div>
					<div class="span8 mobile-span9">
						<p><?=Bluebell_Request::location($request)?>
						<? if ($request->location_string): ?>
							<br />
							<a href="<?=Location_Map::get_directions($request, $this->user)?>" target="_blank" class="small"><?=__('Get directions')?></a></p>
						<? endif ?>
					</div>
				</div>

				<hr />

				<div id="p_ask_question">
					<?=$this->display_partial('job:ask_question', array('can_ask'=>$request->status_code==Service_Status::status_active))?>
				</div>

			</div>
			<? if ($this->user): ?>
			<div class="job_ignore">
				<a href="javascript:;" id="link_request_ignore">
					<i class="icon-ban-circle"></i> 
					<?=__('Ignore this job offer')?>
				</a>
			</div>
			<? endif ?>

		</div>

		<div class="span8">
			<? if ($request->status_code!=Service_Status::status_active): ?>
				<div class="well align-center"><?=__('Sorry, this request has closed for bidding')?></div>
				<? if ($quote): ?>
					<div id="p_quote_panel"><?=$this->display_partial('job:quote_summary', array('is_editable'=>false))?></div>
				<? endif ?>
			<? elseif (!$quote): ?>
				<? if ($request->provider_has_link($provider, Service_Request::link_type_banned)): ?>
					<div class="well align-center"><?=__('Sorry, you have ignored this request and cannot bid')?></div>
				<? else: ?>
					<div id="p_quote_panel"><?=$this->display_partial('job:quote_submit')?></div>
				<? endif ?>
			<? else: ?>
				<div id="p_quote_panel"><?=$this->display_partial('job:quote_summary')?></div>
			<? endif ?>
		</div>

	</div>

	<div id="popup_request_ignore" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header"><h2><?=__('Ignore this job offer?')?></h2></div>
		<div class="modal-body">
			<p class="lead"><?=__('This job offer will no longer be available to you. This can not be undone.')?></p>
		</div>
		<div class="modal-footer">
			<div class="pull-left">
				<a href="javascript:;" class="popup-close"><?=__('I changed my mind')?></a>
			</div>
			<div class="pull-right">
				<a href="javascript:;" class="btn btn-danger btn-large popup-close" id="button_request_ignore" data-request-id="<?=$request->id?>"><?=__('Confirm ignore')?></a>
			</div>
		</div>
	</div>

<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that job could not be found')))?>
<? endif ?>

