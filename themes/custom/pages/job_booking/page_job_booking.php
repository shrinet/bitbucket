<? if ($request && $provider): ?>

	<ul class="breadcrumb">
		<li><a href="<?=root_url('dashboard')?>"><?=__('My Appointments')?></a> <i class="icon-chevron-right divider"></i></li>
		<li class="current"><a href="javascript:;"><?=$this->page->title_name?></a></li>
	</ul>
	<div class="inner_panel">

		<div class="row-fluid">

			<div class="span8">

				<div class="page-header">
					<h1><?=__('Booking with %s', $opp_user_name)?></h1>
					<? if (!$is_cancelled): ?>
						<h4 class="subheader"><?=__("Congrats! You've booked this %s appointment", $request->title)?></h4>
					<? else: ?>
						<h4 class="subheader"><?=__("Appointment cancelled")?></h4>
					<? endif ?>
				</div>

				<? if ($can_rate): ?>
					<div class="well">
						<?=form_open(array('id' => 'form_rating'))?>
							<?=form_hidden('quote_id', $quote->id)?>
							<div id="p_job_rating_form"><?=$this->display_partial('job:rating_form')?></div>
						<?=form_close()?>
					</div>
				<? endif ?>
				
				<div class="well">
					<div id="p_booking_summary"><?=$this->display_partial('job:booking_summary')?></div>
					<hr />
					<?=form_open(array('id' => 'form_conversation', 'class'=>'control_conversation'))?>
						<?=$this->display_partial('control:conversation', array('to_user_id' => $opp_user_id, 'quote' => $quote))?>
					<?=form_close()?>                    
				</div>

			</div>
			<div class="span4">
				<div class="panel job-details radius">

					<?= $this->display_partial('job:details') ?>
					
					<? if ($questions->count): ?>
						<hr />
						<h4><?=__('Job Questions')?></h4>
						<ul id="job_questions">
							<? foreach ($questions as $question): ?>
							<li>
								<? if ($question->answer): ?>
									<a href="javascript:;" class="question"><?=$question->description?></a>
									<span class="answer"><?=$question->answer->description?></span>
								<? else: ?>
									<span class="question"><?=$question->description?></span>
								<? endif ?>
							</li>
							<? endforeach ?>
						</ul>
					<? endif ?>

				</div>
				<? if (!$is_cancelled): ?>
					<hr />
					<div class="job_cancel">
						<a href="javascript:;" id="link_job_cancel">
							<i class="icon-remove-sign"></i> <?=__('Cancel this appointment')?>
						</a>
					</div>
				<? endif ?>
			</div>
			<div class="escrow">
					<? if($escrow->escrow_status == Payment_Escrow::status_funded && $is_provider):?>
					This escrow have funded on <?=$escrow->funded_at?>
						<?if(!$escrow->is_requested):?>
						<a href="javascript:;" id="request_escrow">
							<i class="icon-remove-sign"></i> <?=__('Request release Escrow')?>
						</a>
						<? endif ?>
					<? elseif(!$is_provider): ?>
					This escrow have funded on <?=$escrow->funded_at?>
					<?if($escrow->is_requested):?>
						<a href="javascript:;" id="request_escrow">
							<i class="icon-remove-sign"></i> <?=__('Approve Request')?>
						</a>
						<? endif ?>
					<? endif ?>
			</div>

		</div>

	</div>

	<? if ($is_success): ?>
		<div id="popup_success_message" class="modal hide fade" tabindex="-1" role="dialog">
			<div class="modal-header">
				<h2><?=__('Your %s job has been booked!', $request->title)?></h2>
			</div>
			<div class="modal-body">
				<p class="lead"><?=__('Help us grow by sharing this with your friends.')?></p>
				<div class="row-fluid">
					<div class="span6 mobile-span2">
						<p>
							<img src="<?=theme_url('assets/images/social/facebook.png')?>" alt="Facebook" class="social_icon" />
							<a href="<?=Social_Share::facebook($request->get_url('job/booking',true), __('Just booked a %s on %s!', array($request->title, c('site_name'))), c('site_name'))?>" target="_blank">
								<?=__('Share on Facebook')?>
							</a>
						</p>
					</div>
					<div class="span6 mobile-span2">
						<p>
							<img src="<?=theme_url('assets/images/social/twitter.png')?>" alt="Twitter" class="social_icon" />
							<a href="<?=Social_Share::twitter($request->get_url('job/booking',true), __('Just booked a %s on %s!', array($request->title, c('site_name'))))?>" target="_blank">
								<?=__('Share on Twitter')?>
							</a>
						</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="popup-close"><?=__('No thanks')?></a>
			</div>
		</div>
		<script> $('#popup_success_message').popup({ autoReveal:true }); </script>
	<? endif ?>

	<div id="popup_job_cancel" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h2><?=__('Cancel this booking?')?></h2>
		</div>
		<div class="modal-body">
			<p class="lead"><?=__('Are you sure you want to cancel this booking? The other party will be notified of your decision.')?></p>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="popup-close pull-left"><?=__('I changed my mind')?></a>
			<a href="javascript:;" class="btn btn-danger popup-close" id="button_job_cancel" data-quote-id="<?=$quote->id?>"><?=__('Cancel booking')?></a>
		</div>
	</div>
		<!--Escrow release-->
	<?if($is_provider):?>
	<div id="popup_request_release" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h2><?=__('Request Release?')?></h2>
		</div>
		<div class="modal-body">
			<p class="lead"><?=__('Are you sure you want to Request Release? The other party will be notified of your decision.')?></p>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="popup-close pull-left"><?=__('I changed my mind')?></a>
			<a href="javascript:;" class="btn btn-danger popup-close" id="button_request_release" data-escrow-id="<?=$escrow->id?>"><?=__('Request Release')?></a>
		</div>
	</div>
	<? else: ?>
		<div id="popup_request_release" class="modal hide fade" tabindex="-1" role="dialog">
			<div class="modal-header">
				<h2><?=__('Approve Request?')?></h2>
			</div>
			<div class="modal-body">
				<p class="lead"><?=__('Are you sure you want to Approve Request? The amount will be released to other party.')?></p>
			</div>
			<div class="modal-footer">
				<a href="javascript:;" class="popup-close pull-left"><?=__('I changed my mind')?></a>
				<a href="javascript:;" class="btn btn-danger popup-close" id="button_request_release" data-escrow-id="<?=$escrow->id?>"><?=__('Approve Request')?></a>
			</div>
		</div>
	<? endif ?>
<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that job booking could not be found')))?>
<? endif ?>