<?
	$is_newuser = !$this->user || ($this->user && !$this->user->is_requestor && !$this->user->is_provider);
?>

<? if ($is_newuser): ?>
	<?=$this->display_partial('dash:new_user')?>
<? else: ?>
	<div class="row-fluid">

		<div class="span3">

			<div id="p_dash_booking_panel">
				<?=$this->display_partial('dash:booking_panel')?>
			</div>

			<div id="p_dash_message_panel">
				<?=$this->display_partial('dash:message_panel')?>
			</div>

			<div id="p_dash_review_panel">
				<?=$this->display_partial('dash:review_panel')?>
			</div>

		</div>

		<div class="span9">
			<div id="p_dash_welcome">
				<?=$this->display_partial('dash:welcome')?>
			</div>
			<? if ($this->user->is_requestor): ?>
				<div id="p_requests" class="box">
					<div class="box-header">
						<h6><?=__('My Requests')?></h6>
					</div>
					<div class="box-content offer-request-control">
						<div id="p_dash_requests">
							<?=$this->display_partial('dash:requests')?>
						</div>
					</div>
					<div class="box-footer">
						<a href="<?=root_url('account/requests')?>">
							<i class="icon-plus-sign"></i> 
							<?=__('View my service requests')?>
						</a>
					</div>
				</div>
			<? endif ?>

			<? if ($this->user->is_provider): ?>
				<div id="p_offers" class="box">
					<div class="box-header">
						<h6><?=__('Job Offers')?></h6>
					</div>
					<div class="box-content offer-request-control">
						<div id="p_dash_offers">
							<?=$this->display_partial('dash:offers')?>
						</div>
						<? if (!$jobs->count): ?>
							<p><?=__('There are no active service requests')?></p>
						<? endif ?>
					</div>
					<? if ($jobs->count): ?>
						<div class="box-footer">
							<a href="<?=root_url('account/offers')?>">
								<i class="icon-plus-sign"></i> 
								<?=__('View more job offers')?>
							</a>
						</div>
					<? endif ?>
				</div>
			<? endif ?>

		</div>

	</div>
<? endif ?>
