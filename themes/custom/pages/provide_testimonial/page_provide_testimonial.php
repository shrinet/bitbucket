<? if ($provider): ?>
	<div class="page-header">
		<h1><?=$this->page->title_name?></h1>
		<h4 class="subheader"><?=__("Write a testimonial for %s's %s service", array($provider->business_name, $provider->role_name))?></h4>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<div id="p_provide_testimonial_write_form">
				<?=form_open(array('id' => 'form_provide_testimonial_write', 'class'=>'nice'))?>			
					<input type="hidden" name="provider_id" value="<?= $provider->id ?>" />
					<input type="hidden" name="hash" value="<?= $testimonial->hash ?>" />
					<?=$this->display_partial('provide:testimonial_write_form')?>
					<?=form_submit('submit', __('Submit testimonial'), 'class="btn btn-primary btn-large"')?>
				<?=form_close()?>
			</div>
			<div id="provide_testimonial_write_success" style="display:none">
				<div class="alert alert-success"><?=__('Thank you for submitting a testimonial for %s.', $provider->business_name)?></div>
			</div>
		</div>
		<div class="span6">
			<div class="well">
				<?=content_block('testimonial_intro', 'Testimonial Introduction', array('business_name'=>$provider->business_name))?>
			</div>
		</div>
	</div>
<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that provider could not be found')))?>
<? endif ?>
