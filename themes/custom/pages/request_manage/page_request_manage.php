<ul class="breadcrumb">
	<li><a href="<?=root_url('dashboard')?>"><?=__('My Service Requests')?></a> <i class="icon-chevron-right divider"></i></li>
	<li class="active"><?=$this->page->title_name?></li>
</ul>

<div class="inner_panel">
	<? if ($request): ?>

	<div class="row-fluid">

		<div class="span8">

			<? if ($request->is_new && $request->status_code != Service_Status::status_cancelled): ?>
				<div class="page-header">
					<h1><?=__('%s request submitted', '<strong>'.$request->title.'</strong>')?></h1>
					<h4 class="subheader"><?=__('Your request has been submitted. What happens next?')?></h4>
				</div>
				<div class="localSuggestionButton">
					<div class="row-fluid">
						<div class="span12">
							<a href="<?=$request->get_url('request/providers')?>" class="btn btn-default btn-lg btn-block">SEARCH FOR PRO'S IN YOUR AREA<i class="icon-chevron-right"></i> </a>
						</div>
					</div>
				</div>
				<div class="request-complete">
					<?=content_block('request_complete', 'Request submitted', array(
						'title'=>$request->title, 
						'title_plural'=>Phpr_Inflector::pluralize($request->title)
					))?>
				</div>
				<hr />
			<? else: ?>
				<div class="page-header">
					<h1><?=__('%s Request', '<strong>'.$request->title.'</strong>', true)?></h1>
				</div>
			<? endif ?>

			<div id="p_details">
				<?=$this->display_partial('request:manage_details')?>
			</div>
			
			<? if ($show_questions): ?>
				<a name="questions"></a>
				<h4><?=__('Questions')?></h4>
				<div id="p_questions">
					<?=$this->display_partial('request:questions')?>
				</div>
			<? endif ?>

		</div>

		<div class="span4">
			<div id="p_status_panel" class="box">
				<?=$this->display_partial('request:status_panel')?>
			</div>
			<? if ($can_edit): ?>
				<div class="request_cancel">
					<?=__('%s this service request', '<a href="javascript:;" id="link_request_cancel"><i class="icon-remove-sign"></i> '.__('Cancel', true).'</a>')?>
				</div>
			<? endif ?>
		</div>

	</div>

	<? if ($show_quotes): ?>
		<a name="quotes"></a>
		<h4><?=__('Quotes')?></h4>
		<div id="p_quotes">
			<?=$this->display_partial('request:quotes')?>
		</div>
	<? endif ?>

	<div id="popup_request_cancel" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header"><h2><?=__('Cancel this service request?')?></h2></div>
		<div class="modal-body">
			<p class="lead"><?=__('Are you sure you want to cancel this request? This request and all quotes will be lost!')?></p>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="btn btn-danger popup-close" id="button_request_cancel" data-request-id="<?=$request->id?>"><?=__('Cancel this request')?></a>
			<a href="javascript:;" class="pull-left popup-close"><?=__('Continue request')?></a>
		</div>
	</div>

	<? else: ?>
		<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that request could not be found')))?>
	<? endif ?>

</div>