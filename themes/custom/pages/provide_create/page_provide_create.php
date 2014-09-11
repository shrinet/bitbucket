<div class="row-fluid">
	<div class="span9">
		<?=form_open(array('id' => 'form_provide_create', 'class'=>'nice'))?>

			<div class="providerBox">
				<div class="page-header">
					<h1><?=__('Create your profile')?></h1>
					<h4 class="subheader"><?=__('Complete your profile and connect with new customers today, Research shows that service providers who fill out their profiles entirely are more likely to win bids.')?></h4>
				</div>
				<div class="box-content">

					<div class="row-fluid">
						<div class="span8">
							<div class="well">
								
								<div id="p_profile_form">
									<?=$this->display_partial('provide:profile_form')?>
								</div>

							</div>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span12">
							<h4><?=__('About Your Company')?></h4>
							<div id="p_provide_description"><?=$this->display_partial('provide:description_short_form')?></div>
						</div>
					</div>

					<h4><?=__('Your hours')?> <a href="javascript:;" id="link_work_hours" data-toggle-text="<?=__('Show less options')?>"><?=__('Show more options')?></a></h4>
					<div id="p_work_hours_form">
						<?=$this->display_partial('provide:work_hours_form')?>
					</div>

					<hr />

					<h4><?=__('Your work area')?></h4>
					<div id="p_work_radius_form">
						<?=$this->display_partial('provide:work_radius_form')?>
					</div>

					<hr />

					<p><label for="agree_terms" class="checkbox">
						<input id="agree_terms" type="checkbox"> <?=__('I agree to the Terms and Conditions')?>
					</label></p>

				</div>
			</div>

			<div class="form-actions align-center">
				<?=form_submit('save', __('Save profile'), 'class="btn btn-primary btn-large"')?>
			</div>

		<?=form_close()?>		

	</div>
	<div class="sidebar span3">
		<?=content_block('provider_create_sidebar', 'Sidebar content') ?>
		<?//=Social_Facebook::facepile()?>
	</div>
</div>