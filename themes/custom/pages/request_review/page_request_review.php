<? if ($request): ?>
	<?= form_open(array('id' => 'review_form', 'class'=>'')) ?>
		<input type="hidden" name="redirect_request" value="request/manage" />

		<div class="page-header">
			<h1><?=__('Review and submit your request')?></h1>
			<h4 class="subheader"><?=__('%s will send your request to Home Service Professionals available to do your work at the time you specified. Interested Pros will respond with their best price quotes.', array(c('site_name')))?></h4>
		</div>

		<div id="p_review_form">
			<?=$this->display_partial('request:review_form', array('request'=>$request))?>
		</div>

		<? if (!$this->user): ?>
			<fieldset class="form-horizonta">
				<div class="row-fluid" id="title_register">
					<div class="span8">
						<h3><?=__('Last step - Create account and submit request!')?></h3>
					</div>
					<div class="span4">
						<a href="javascript:;" id="toggle_login" 
							onclick="Page.toggleAccountRequest()" 
							class="pull-right btn">
							<?= __('Already have an account? Log in') ?>
						</a>
					</div>
				</div>
				
				<div class="row-fluid" id="title_login" style="display:none">
					<div class="span8">
						<h3><?=__('Last step - Log in and submit request!')?></h3>
					</div>
					<div class="span4">
						<a href="javascript:;" id="toggle_register" 
							onclick="Page.toggleAccountRequest()" 
							class="pull-right btn">
							<?= __("Don't have an account yet? Sign up") ?>
						</a>
					</div>
				</div>
				<div class="span10">
					<!-- Login form -->
					<div class="loginPanel row-fluid">
						<div class="span7 form">
								<div id="p_account">
									<?=$this->display_partial('site:register_form')?>
								</div>
								<p class="termsText">By pressing submit you agree to the <a href="/terms" title="Terms & Service">Terms & Service</a></p>
								
								<div class="form-actions">
									<button name="review" type="submit" class="pull-right btn btn-large btn-success btn-icon">
										<?=__('Confirm Request')?> <i class="icon-ok"></i>
									</button>
									<a class="forgotPassword" href="<?=root_url('account/reset')?>"><?=__('Forgot Password?')?></a>
								</div>
						</div>

						<div class="span1 divider">
							<div class="bl-line"><img src="/themes/custom/assets/images/bl-line.png"></div>
							OR
							<div class="gr-line"><img src="/themes/custom/assets/images/gr-line.png"></div>
						</div>

						<div class="socialPanel span4">
							<!-- Social Authentication -->
							<? if (Social_Config::can_authenticate()): ?>
								<h4><?= __('Click to login with') ?></h4>
								<? foreach (Social_Provider::find_all_active_providers() as $provider): ?>
									<p>
										<a href="<?=$provider->get_login_url()?>" class="btn btn-large btn-block <?=$provider->code?>">
											<i class="fa fa-<?=$provider->code?>"></i> Sign in with <?=$provider->provider_name?>
										</a>
									</p>
								<? endforeach ?>
								<h5><?= __('Why?') ?></h5>
							<? endif ?>
						</div>
					</div> <!-- End loginPanel-->
				</div>
			<? else: ?>
			<div class="loginPanel row-fluid">
				<div class="span7 form">
						<p class="termsText">By pressing submit you agree to the <a href="/terms" title="Terms & Service">Terms & Service</a></p>
						
						<div class="form-actions">
							<button name="review" type="submit" class="pull-right btn btn-large btn-success btn-icon">
								<?=__('Confirm Request')?> <i class="icon-ok"></i>
							</button>
						</div>
				</div>
			</div>
			<? endif ?>
	<? if (!$this->user): ?>
		</fieldset>
	<? endif ?>

	<?= form_close() ?>
<? else: ?>
	<?= $this->display_partial('site:404', array('error_message'=>__('Sorry, something went wrong with your request. Please check that your browser has cookies enabled or change your browser settings to default.'))) ?>
<? endif ?>
