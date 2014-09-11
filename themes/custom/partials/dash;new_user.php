<div class="row">
	<div class="span9">

		<h3><?=__('How would you like to start using %s?', c('site_name'))?></h3>

		<div class="tabbable">
			 <ul class="nav nav-tabs">
				 <li class="active"><a href="#request_services" data-toggle="tab"><?=__('Request Services')?></a></li>
				 <li><a href="#provide_services" data-toggle="tab"><?=__('Provide Services')?></a></li>
			 </ul>
			<div class="tab-content">
				<div class="tab-pane active" id="request_services">
					<?=form_open(array('action'=>root_url('request'), 'id'=>'submit_request'))?>
						<div class="row-fluid">
							<div class="span5 align-center">
								<?=form_label(__('What service are you looking for?'))?>
							</div>
							<div class="span7">
								
									<div class="input-append">
										<input type="text" name="role_name" value="" class="oversize" id="request_title" />
										<?=form_submit('submit', 'Get started', 'class="btn btn-primary"')?>
									</div>

								<div class="small"><?=__('eg: Painter, Carpenter, Electrician')?></div>
								<script> Page.bindRoleSelect('#request_title'); </script>
							</div>
						</div>
					<?=form_close()?>
				</div>
				<div class="tab-pane" id="provide_services">
					<?=form_open(array('id'=>'create_profile', 'onsubmit'=>"window.location = '".root_url('provide/create')."';return false"))?>
						<div class="row-fluid">
							<div class="span6 align-center">
								<?=form_label(__('Create a profile and get job leads!'))?>
							</div>
							<div class="span6">
								<?=form_submit('submit', 'Create profile', 'class="btn btn-primary"')?>
							</div>
						</div>
					<?=form_close()?>
				</div>
			</div>
		</div>

	</div>
</div>