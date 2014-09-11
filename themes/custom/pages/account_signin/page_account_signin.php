<div class="row-fluid">
	<div class="span11" style="margin-left: 5%;padding: 18px;">
		
			<div class="page-header"><h1><?=$this->page->title_name?></h1><h4><?= __("Don't have an account yet? ") ?> <a href="<?= root_url('account/signup') ?>"><?= __('Sign Up') ?></a></h4></div>
			
			<div class="loginPanel row-fluid">

				<!-- Login form -->
				<div class="span7 form">
					<?=form_open(array('id' => 'form_login'))?>
						<input type="hidden" name="redirect" value="<?= (isset($redirect))?$redirect:root_url('dashboard') ?>" />
						
						<?=$this->display_partial('site:login_form')?>
						<p class="termsText">By pressing submit you agree to the <a href="/terms" title="Terms & Service">Terms & Service</a></p>
						<p class="loginBtnPanel">
							<button name="submit" type="submit" class="pull-right btn btn-large btn-primary btn-icon login"><?= __('LOG IN') ?><i class="icon-chevron-right"></i></button>
							<a class="forgotPassword" href="<?=root_url('account/reset')?>"><?=__('Forgot Password?')?></a>
						</p>
					<?=form_close()?>
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
		
	</div><!-- End span10-->
</div><!-- End row-fluid-->

