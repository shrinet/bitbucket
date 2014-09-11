<? if ($provider): ?>
<div class="row-fluid">
	<div class="span9">
		<a href="<?=root_url('provide/create')?>" class="pull-right btn btn-primary">
			<i class="icon-plus"></i> 
			<?=__('Create another profile')?>
		</a>

		<div class="page-header">
			<h1><?=__('%s Profile', $provider->role_name)?> <small><a href="javascript:;" id="link_delete_profile"><?=__('delete this profile')?></a></small></h1>
			<h4 class="subheader"><?=__('Below is how %s customers will see your profile. Research shows that service providers who fill out their profiles entirely and more likely to win bids.', c('site_name'))?></h4>
		</div>

		<div class="box">
			<div class="box-header">
				<h3><?=__('Manage your profile')?></h3>
			</div>
			<div class="box-content">

				<div class="row-fluid">
					<div class="span8">
						<div class="well">
							<div id="p_profile_form">
								<?=form_open(array('id' => 'form_provide_details', 'class'=>'nice'))?>
									<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
									<?=$this->display_partial('provide:profile_form')?>
								<?=form_close()?>
							</div>
						</div>
					</div>
				</div>
				
				<!--add Membership-->
				<div class="row-fluid">
					<div class="span8 membershipPanel">
						<div class="well">
							<div id="p_profile_form">
								<?=$this->display_partial('provide:membership_form')?>
							</div>
						</div>
					</div>
				</div>
				<!--end Membership-->
				
				<hr />
				<div class="row-fluid">
					<div class="span12">
						<a href="javascript:;" id="button_profile_portfolio" class="button_profile_portfolio pull-right btn btn-small"><?=__('Add / Remove Photos')?></a>
						<h4><?=__('Portfolio')?></h4>
						<div id="p_provide_portfolio"><?=$this->display_partial('provide:portfolio', array('images'=>$provider->get_portfolio(), 'is_manage'=>true))?></div>
					</div>
				</div>
				<hr />
				<div class="row-fluid">
					<div class="span12">
						<h4><?=__('About %s', $provider->business_name)?></h4>
						<div id="p_provide_description"><?=$this->display_partial('provide:description')?></div>
					</div>
				</div>
				<? /* @todo 
				<hr />
				<div class="row-fluid">
					<div class="span12">
						<h4><?=__('Services')?></h4>
					</div>
				</div>
				*/ ?>
				<hr />
				<div class="row-fluid">
					<div class="span12">
						<a href="javascript:;" id="button_profile_testimonials" class="pull-right btn btn-small"><?=__('Get testimonials')?></a>
						<h4><?=__('Testimonials')?></h4>
						<? if ($provider->testimonials->count): ?>
							<div id="p_provide_testimonials"><?=$this->display_partial('provide:testimonials')?></div>
						<? else: ?>
							<div class="well testimonials">
								<div class="row-fluid"><?=content_block('provider_testimonials', 'Why get testimonials?')?></div>
							</div>
						<? endif ?>
					</div>
				</div>

			</div>
		</div>
		<div class="form-actions align-center">
			<?=form_button('done', __('Done', true), 'class="btn btn-primary btn-large" id="button_done"')?>
		</div>
	</div>
	<div class="span3">

		<div class="well">
			<h4><?=__('Modify')?></h4>
			<ul class="square">
				<li><a href="javascript:;" id="link_work_hours"><?=__('My hours')?></a></li>
				<li><a href="javascript:;" id="link_work_area"><?=__('My work area')?></a></li>
				<li><a href="<?=root_url('account/notifications')?>"><?=__('My notifications')?></a></li>
			</ul>
		</div>

		<!--div id="p_dash_top_provider_panel"><?=$this->display_partial('dash:top_provider_panel')?></div-->

		<a href="<?=$provider->get_url('profile')?>" class="btn btn-block"><?=__('View my profile page')?></a>

	</div>
</div>

<!-- Profile Complete -->
<div id="popup_profile_success" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-body">
		<?=content_block('provider_success', 'Profile has been set up', array('role_name'=>$provider->role_name, 'site_name'=>c('site_name')))?>
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="btn popup-close" onclick=""><?=__('Close',true)?></a>
	</div>
</div>

<!-- Delete Profile -->
<div id="popup_profile_delete" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header"><h2><?=__('Delete your %s profile', $provider->role_name)?></h2></div>
	<div class="modal-body">
		<p class="lead"><?=__('Are you sure you want to delete this profile? This action cannot be reversed.')?></p>
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="popup-close pull-left"><?=__('I changed my mind')?></a>
		<a href="javascript:;" class="btn btn-danger btn-large popup-close" id="button_profile_delete" data-provider-id="<?=$provider->id?>"><?=__('Delete this profile')?></a>
	</div>
</div>


<!-- Membership Profile -->
<div id="popup_profile_membership" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header"><h2><?=__('Update your %s membership', $provider->role_name)?></h2></div>
	<div class="modal-body">
	<?=form_open(array('id' => 'form_provide_membership', 'class'=>'nice'))?>
	<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
	<input type="hidden" name="redirect" value="<?=root_url('payment/pay/%s')?>" />
		<?
		$has_membership = ($provider&&$provider->plan_id);
		
		$pops = Service_Plan::get_object_list();
		foreach($pops as $pop){ ?>
			<div class="membershipSinglePanel mBoxLight">
				<div class="mBoxDark">
					<div class="mBoxTitle"><? echo $pop->name; ?></div>
					<div class="mBoxPrice"><span>$</span><? echo $pop->price + 0; ?></div>
				</div>
				<div class="mBoxSubDesc"><? if($pop->credits == 2500) { echo 'Unlimited'; } else { echo $pop->credits; } ?> Bid Intros per Month</div>
				<div class="mBoxDesc"></div>
				<label class="membershipLinks">
					select plan
					<? echo form_radio('Provider[plan_id]',$pop->id , ($pop->id == form_value($provider, 'plan_id') ? true : false)); ?>
				</label>
			</div>
		<? } ?>
	</div>
	<div class="modal-footer">
		<a href="javascript:;" class="popup-close pull-left"><?=__('I changed my mind')?></a>
		<!--a href="javascript:;" class="btn btn-danger btn-large popup-close" id="button_profile_membership" data-provider-id="<?=$provider->id?>"><?=__('Update Membership')?></a-->
		<input type="submit" class="btn btn-success btn-large" value="Done" name="done">
	</div>
	<?=form_close()?>
</div>

<!-- Portfolio -->
<div id="popup_profile_portfolio" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header"><h2><?=__('Add photos or prior work')?></h2></div>
	<?=form_open(array('id' => 'form_provide_portfolio', 'class'=>'nice'))?>
		<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
		<div class="modal-body">
			<div id="p_provide_portfolio_form"><?=$this->display_partial('provide:portfolio_form')?></div>
		</div>
		<div class="modal-footer">
			<?=form_submit('done', __('Done', true), 'class="btn btn-primary btn-large popup-close" onclick="return false" id="button_profile_portfolio_close"')?>
		</div>
	<?=form_close()?>
</div>

<!-- Profile Description -->
<div id="popup_profile_description" class="modal hide fade" tabindex="-1" role="dialog">
	<?=form_open(array('id' => 'form_provide_decription', 'class'=>'nice'))?>
		<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
		<div class="modal-header"><h2><?=__('About')?></h2></div>
		<div class="modal-body">
			<div id="p_provide_description_form"><?=$this->display_partial('provide:description_form')?></div>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="popup-close pull-left"><?=__('Cancel', true)?></a>
			<?=form_submit('done', __('Done', true), 'class="btn btn-success btn-large" id="button_profile_description_submit"')?>
		</div>
	<?=form_close()?>
</div>

<!-- Testimonials -->
<div id="popup_profile_testimonials" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header"><h2><?=__('Get Testimonials')?></h2></div>
	<?=form_open(array('id' => 'form_provide_testimonials', 'class'=>'nice'))?>
		<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
		<div class="modal-body">
			<p><?=__('Message will be sent from %s and is not a bulk email, the recipient will only see their email address.', $this->user->email)?></p>
			<fieldset>
				<div id="p_profile_testimonials"><?=$this->display_partial('provide:testimonials_form')?></div>
			</fieldset>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="popup-close pull-left"><?=__('Cancel', true)?></a>
			<?=form_submit('send', __('Send', true), 'class="btn btn-success btn-large" id="button_profile_testimonials_submit"')?>
		</div>
	<?=form_close()?>

	<div id="profile_testimonials_success" style="display:none">
		<div class="modal-body">
			<p class="lead"><?=__('Your request has been sent successfully')?></p>
			<p><?=__('We will send you an email telling you when someone submits a testimonial about you.')?></p>
		</div>
		<div class="modal-footer">
			<?=form_button('close', __('Close', true), 'class="large btn btn-primary popup-close"')?>
		</div>
	</div>
</div>

<!-- Work Radius -->
<div id="popup_work_radius" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header"><h2><?=__('My work area')?></h2></div>
	<?=form_open(array('id' => 'form_work_radius', 'class'=>'nice'))?>
		<input type="hidden" name="Provider[zip]" value="<?= $provider->zip ?>" />
		<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
		<div class="modal-body">
			<div id="p_work_radius_form"><?=$this->display_partial('provide:work_radius_form')?></div>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="popup-close pull-left"><?=__('Cancel', true)?></a>
			<?=form_submit('done', __('Done', true), 'class="btn btn-success btn-large"')?>
		</div>
	<?=form_close()?>
</div>

<!-- Work Hours -->
<div id="popup_work_hours" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-header"><h2><?=__('My work hours')?></h2></div>
	<?=form_open(array('id' => 'form_work_hours', 'class'=>'nice'))?>
		<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
		<div class="modal-body">
			<div id="p_work_hours_form"><?=$this->display_partial('provide:work_hours_form')?></div>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="popup-close pull-left"><?=__('Cancel', true)?></a>
			<?=form_submit('done', __('Done', true), 'class="btn btn-success btn-large"')?>
		</div>
	<?=form_close()?>
</div>

<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that profile could not be found')))?>
<? endif ?>