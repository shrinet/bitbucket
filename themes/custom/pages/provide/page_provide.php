<?
	$selected_tab = $this->request_param(0, 'provide-services');
?>


<div class="tabbable">

	<div class="row-fluid">
		<div class="span11" style="margin-left: 5%;padding: 18px;">

					<div class="page-header">
						<h1><?=__('Register as a Home Service Pro', c('site_name'))?></h1>
						<h4 class="subheader"><?=__('Recieve free leads from customers who are looking for your services')?></h4>
					</div>

					<? if (!$this->user): ?>

						<?=form_open(array('id' => 'form_register', 'class'=>'custom nice'))?>
								<?=form_hidden('redirect', root_url('provide/create'))?>
									<!-- Login form -->
									<div class="loginPanel row-fluid">
										<div class="span7 form">
												<div>
													<?=$this->display_partial('site:register_form')?>
												</div>
												<p class="termsText">By pressing submit, you agree to the <a href="/terms" title="Terms & Service">Terms & Service</a> and also that you have authority to claim the business account on behalf of this business.</p>
												
												<div class="form-actions">
													<button name="review" type="submit" class="pull-right btn btn-large btn-success btn-icon">
														<?=__('Create Service Profile')?> <i class="icon-ok"></i>
													</button>
													
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
						<?=form_close()?>

					<? else: ?>
						<a href="<?=root_url('provide/create')?>" class="btn btn-large btn-primary"><?=__('Create Service Profile')?></a>
					<? endif?>

		</div>
	</div>
</div>

