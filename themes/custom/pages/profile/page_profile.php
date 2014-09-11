<? if ($provider): ?>
<!--ul class="breadcrumb">
	<li><a href="<?=root_url('directory')?>"><?=__('Directory',true)?></a></li>
	<?  
	$dir_url = root_url('directory/a');
	?>
	<li><a href="<?=$dir_url.='/'.strtolower($provider->country->code)?>"><?=$provider->country->name?></a> <i class="icon-chevron-right divider"></i></li>
	<li><a href="<?=$dir_url.='/'.strtolower($provider->state->code)?>"><?=$provider->state->name?></a> <i class="icon-chevron-right divider"></i></li>
	<li><a href="<?=$dir_url.='/'.Phpr_Inflector::slugify($provider->city)?>"><?=$provider->city?></a> <i class="icon-chevron-right divider"></i></li>
	<li><a href="<?=$dir_url.='/'.$provider->categories->first->url_name?>"><?=$provider->role_name?></a> <i class="icon-chevron-right divider"></i></li>
	<li class="active"><?= $provider->business_name ?></li>
</ul-->
<div class="page-header">
	<h1><?=$this->page->title_name?></h1>
	<h4 class="subheader"><?=$this->page->description?></h4>
</div>
<div class="row-fluid">
	<div class="span12">
	<div class="span8 secLeft">
	  
		<? if ($portfolio): ?>
			<div id="portfolio-images" class="carousel slide" data-ride="carousel">
				<!-- Carousel items -->
				<div class="carousel-inner">
					<?
						$count = 0;
					?>
					<? foreach ($portfolio as $item): ?>
						<div class="<?=$count==1?'active':''?> item">
							<img src="<?=$item->image?>" alt="" />
						</div>
					<? $count++; endforeach ?>
				</div>
				
				<div class="controlls-sec">
					<ul class="carousel-indicators">
						<?
							$count = 0;
						?>
						<? foreach ($portfolio as $item): ?>
							<li data-target="#portfolio-images" data-slide-to="<?=$count?>" class="<?=$count==1?'active':''?>"><img src="<?=$item->image?>" alt="" /></li>
						<? $count++; endforeach ?>
					</ul>
				</div>
			
			</div>
			
		<? endif ?>

		<h4 class="hborder title"><?=__('About %s', $provider->business_name)?></h4>
		<div class="businessDesc"><?= $provider->description_html ?></div>
		
	
		
		<div class="reviews_mvs row-fluid">
			<div class="span12">
				<h4 class="hborder title"><?=__('leave a review for %s', $provider->business_name)?></h4>
				<?=form_open(array('id' => 'form_rating'))?>
				<input type="hidden" name="Provider[id]" value="<?= $provider->id ?>" />
				<? if(isset($jobs->first)){ ?>
				<input type="hidden" name="job" value="<?= $jobs->first->id;?>" /><? } ?>
				<div class="span4 reset">
					<div class="rate-box">
						<div class="round-li">1</div>
						<span class="rate">Rate This Contractor</span>
						<div class="controls">
							<div class="rating-selector" id="rating_rating">
								<?=form_dropdown('Rating[rating]', array(0,1,2,3,4,5))?>
							</div>
						</div>
					</div>
					<br />
					<div class="rate-dis">
						<p>Click stars to rate</p>
					</div>	
				</div>
				<script type="text/javascript"> jQuery(document).ready(function($) { $('#rating_rating').starRating(); }); </script>
				<div class="span8">
				<div class="rate-box">
					<div class="round-li">2</div>
					<span class="rate">Provide a detailed review of this contrator</span>
					<textarea class="span10 reviewBoxTextarea" name="Rating[comment]" placeholder="What was you experience?"></textarea>
					<button id= "button_rating_provider" data-provider-id="<?=$provider->id?>" class="btn btn-large btn-primary btn-icon" type="submit" name="submit">
					Submit Your Review
				</button>
				</div>
				<?=form_close()?>
			</div></div>
		</div>

	</div>
	<div class="span4">
		<div class="generalinfo">
			<h4 class="hborder title textright"><?=__('General Information')?></h4>
			<div id="p_control_badge" class="hide-for-small">
				<? if ($user): ?>
					<?//= $user->fetched['name'] ?>
				<? endif ?>
				<?=$this->display_partial('control:badge', array('provider'=>$provider))?>
			</div>
		</div>

		<div class="call_to_action">
			<a href="javascript:;" class="btn btn-primary btn-large btn-block" id="button_contact_provider"><?=__('Contact this provider')?></a>
		</div>
		
		<div class="contactinfo">
			<h4 class="hborder2 title textright"><?=__('Contact Information')?></h4>
			<div class="ct-div info-mobile"><span class="text-ct"><?= $provider->mobile ?></span></div>
			<div class="ct-div info-phone"><span class="text-ct"><?= $provider->phone ?></span></div>
			<div class="ct-div info-email"><span class="text-ct"><?//= $provider->email ?></span></div>
			<div class="ct-div info-web"><span class="text-ct"><?= $provider->url ?></span></div>
			<div class="map-ct">
				<iframe width="350" height="200" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDr09FTrMAuDL0IkyP70qx8ffsbf01uGPg&q=<?= urlencode($provider->address_string); ?>"></iframe>
			</div>
			<div class="address">
				<p><?= $provider->address_string ?></p>
			</div>
		</div>
		
		
		
		
		<div class="ratings">
		<p class="overall star-rating">
				<i class="rating-<?=$provider->rating*10?>"></i> 
				<span class="hborder2 title pull-right"><?=__('Ratings')?></span>
			</p>	
		</div>
		<p class="rate-comment"><?=__('Avg rating ')?><?=$provider->rating?>/5 Stars</p>
			<? if ($provider->rating_num > 0): ?>
			<?=$this->display_partial('provide:ratings_small', array(
				'ratings'=>$ratings, 
				'base_url'=>$provider->get_url('profile')
			))?>
		<? endif ?>
		<button name="submit" type="submit" class="btn btn-large btn-primary pull-right btn-icon profile-rating-view-all" id="button_rating_full">View All</button>
	</div>
	</div>
</div>
<hr />
<!-- rating pop up -->
<div id="popup_rating_provider" class="modal hide fade" tabindex="-1" role="dialog">
	<?=form_open(array('id'=>'form_rating_pop'))?>
		<?=form_hidden('invite_providers[]', $provider->id)?>
		<div class="modal-header">
			<h3><?=__('Get a Quote from %s', $provider->business_name)?></h3>
		</div>
		<div class="modal-body">
			
				<?=__('Send this to other %s in %s and let them provide quotes as well', array(Phpr_Inflector::pluralize($provider->role_name), $provider->location_string))?>
			</label>
		</div>
		<div class="modal-footer">
			<?=form_submit('submit', __('Send request'), 'class="btn btn-primary" id="button_submit_contact_provider" data-provider-id="'.$provider->id.'"')?>
		</div>
	<?=form_close()?>
</div>
<!-- contact pop up -->
<div id="popup_contact_provider" class="modal hide fade" tabindex="-1" role="dialog">
	<?=form_open(array('id'=>'form_request'))?>
		<?=form_hidden('invite_providers[]', $provider->id)?>
		<div class="modal-header">
			<h3><?=__('Get a Quote from %s', $provider->business_name)?></h3>
		</div>
		<div class="modal-body">
			<?=$this->display_partial('request:quick_form', array('role'=>$provider->categories->first))?>
			<label class="checkbox open_request" for="open_request">
				<?=form_checkbox('open_request', true, true, 'id="open_request"')?> 
				<?=__('Send this to other %s in %s and let them provide quotes as well', array(Phpr_Inflector::pluralize($provider->role_name), $provider->location_string))?>
			</label>
		</div>
		<div class="modal-footer">
			<?=form_submit('submit', __('Send request'), 'class="btn btn-primary" id="button_submit_contact_provider" data-provider-id="'.$provider->id.'"')?>
		</div>
	<?=form_close()?>
</div>
<!-- full rating-->
<div id="popup_full_ratings" class="modal hide fade" tabindex="-1" role="dialog">
	
		<div class="modal-header">
			<h3><?=__('Ratings')?></h3>
		</div>
		<div class="modal-body">
			<? if ($ratings): ?>
				
			<?endif?>
		</div>
		<div class="modal-footer">
			<?=form_submit('submit', __('Close'), 'class="btn btn-primary" id="button_submit_contact_provider" data-provider-id="'.$provider->id.'"')?>
		</div>
	
</div>
<div id="popup_contact_provider_success" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-body">
		<?=global_content_block('directory_request_submit', 'Directory request submitted')?>
	</div>
	<div class="modal-footer">
		<?=form_button('close', __('Close', true), 'class="btn popup-close"')?>
	</div>
</div>

<? else: ?>
	<?=$this->display_partial('site:404', array('error_message'=>__('Sorry, that profile could not be found')))?>
<? endif ?>