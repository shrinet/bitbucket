<div class="row-fluid">
	<div class="span6 offset3">
		<div class="well">

			<!-- Reset password -->
			<div id="reset_pass_form">
				<h1><?=$this->page->title_name?></h1>
				<h4><?=__("No problem! We can help if you can access your email...")?></h4>
				<?=form_open(array('id' => 'form_reset_pass'))?>
					<input type="hidden" name="password_reset" value="1" />
					<div class="control-group">
						<label for="user_email" class="control-label"><?= __('Enter your Email Address', true) ?></label>
						<div class="controls">
							<input type="text" name="User[email]" value="" id="user_email" class="span12" />
						</div>
					</div>
					<?=form_submit('submit', __('Reset Password'), 'class="btn btn-primary btn-large btn-block"')?>
				<?=form_close()?>
			</div>

			<!-- Success message -->
			<div id="reset_pass_success" style="display:none">
				<h1><?=__('Check your mailbox')?></h1>
				<h4><?=__('Instructions have been sent to your email address, please check your Email for confirmation!')?></h4>

				<?=form_open(array('id' => 'form_login', 'class'=>'', 'onsubmit' => "return $(this).phpr().post('user:on_login').send()"))?>
					<input type="hidden" name="redirect" value="<?= root_url('dashboard') ?>" />
					
					<?=$this->display_partial('site:login_form')?>
					
					<?=form_submit('submit', __('Log in'), 'class="btn btn-primary btn-large btn-block"')?>
				<?=form_close()?>
			</div>

		</div>
	</div>
</div>

<script>

Page.forgotPasswordFormFields = $.phpr.form().defineFields(function() {
	this.defineField('User[email]').required("<?=__('Please specify your email address',true)?>").email("<?=__('Please specify a valid email address',true)?>");
});

</script>
