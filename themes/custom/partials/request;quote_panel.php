<div class="box">
	<div class="box-header">
		<h3><?=__('Quote from %s', $quote->provider->business_name)?></h3>
	</div>
	<div class="box-content">
		<div class="row-fluid">
			<div class="span5 push-seven mobile-span3">
				<div class="logo align-center"><img src="<?=Bluebell_Provider::logo($quote->provider)?>" alt="" /></div>
			</div>
			<div class="span7 pull-five mobile-span9">
				<div class="price"><?=format_currency($quote->price)?><span>00</span></div>
				<div class="price_summary"><?=Bluebell_Quote::price_summary($quote)?></div>
				<? if (Bluebell_Quote::price_terms($quote)): ?>
					<div class="price_terms">* <?=Bluebell_Quote::price_terms($quote)?></div>
				<? endif ?>
				<div id="p_request_booking_time_<?=$quote->id?>"><?=$this->display_partial('request:booking_time', array('quote'=>$quote))?></div>
			</div>
		</div>
		<div class="quote_description">
			<?=$quote->comment_html?>
		</div>
	</div>
	<div class="box-footer">
		<a href="javascript:;" class="btn btn-success btn-icon" id="button_quote_<?=$quote->id?>">
			<i class="icon-ok"></i>
			<?=__('Accept quote')?>
		</a>
		<span class="conversation_question">&nbsp; or <a href="javascript:;" id="button_question_<?=$quote->id?>"><?=__('Ask a question')?></a></span>
		<p class="info"><?=__('Click Accept Quote to accept the quote and schedule the job')?></p>
		<?=form_open(array('id' => 'form_conversation_'.$quote->id, 'class'=>'control_conversation'))?>
			<?=$this->display_partial('control:conversation', array('quote'=>$quote))?>
		<?=form_close()?>
	</div>
</div>

<div class="row-fluid">
	<div class="span2">
		<img src="<?=Bluebell_Provider::avatar($quote->provider)?>" width="75" height="75" alt="" />
	</div>
	<div class="span10">
		
		<h3><?=$quote->provider->business_name?></h3>
		<? if ($quote->provider->url): ?><a href="<?=$quote->provider->url?>"><?=$quote->provider->url?></a><? endif ?>
		<div class="location"><?=$quote->provider->location_string?></div>
		<div class="summary">
			<?=$quote->provider->description_html?>
		</div>
		
		<? if ($quote_portfolio = $quote->provider->get_portfolio()): ?>
			<h5><?=__('Portfolio')?></h5>
			<div class="portfolio">
				<?=$this->display_partial('provide:portfolio', array('images'=>$quote_portfolio, 'container_id'=>'provider_portfolio_'.$quote->provider->id))?>
			</div>
		<? endif ?>

		<h5><?=__('Reviews')?></h5>
		<p class="overall star-rating">
			<?=__('Average rating')?>: <i class="rating-<?=$quote->provider->rating*10?>"></i> 
			<span>
				<? if ($quote->provider->rating_num > 0): ?>
					(<?=__('%s reviews', $quote->provider->rating_num)?>)
				<? else: ?>
					(<?=__('No ratings yet')?>)
				<? endif?>
			</span>
		</p>

		<div class="tabbable"> 
			<ul class="nav nav-tabs">
				<li class="active"><a href="#allreviews<?=$quote->provider->id?>" data-toggle="tab"><?=__('All reviews')?></a></li>
				<li><a href="#fivestar<?=$quote->provider->id?>" data-toggle="tab"><?=__('View 5 star reviews')?></a></li>
				<li><a href="#testimonials<?=$quote->provider->id?>" data-toggle="tab"><?=__('Testimonials')?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="allreviews<?=$quote->provider->id?>">
					<?=$this->display_partial('provide:ratings', array('ratings'=>$quote->provider->find_ratings()))?>
				</div>
				<div class="tab-pane" id="fivestar<?=$quote->provider->id?>">
					<?=$this->display_partial('provide:ratings', array('ratings'=>$quote->provider->find_ratings()->where('rating=5')))?>
				</div>
				<div class="tab-pane" id="testimonials<?=$quote->provider->id?>">
					<?=$this->display_partial('provide:ratings', array('ratings'=>$quote->provider->find_testimonials()))?>
				</div>
			</div>
		</div>

	</div>
</div>

<div id="popup_quote_<?=$quote->id?>" class="quote_popup modal hide fade" tabindex="-1" role="dialog">
	<?=form_open(array('id' => 'form_quote_'.$quote->id))?>
		<input type="hidden" name="quote_id" value="<?= $quote->id ?>" />
		<input type="hidden" name="request_id" value="<?= $request->id ?>" />
		<input type="hidden" name="redirect" value="<?= $request->get_url('job/booking').'/success' ?>" />
		<div class="modal-header">
			<h2><?=__('Accept this quote?')?></h2>
		</div>
		<div class="modal-body">
			<div class="quote_details">
				<div class="row-fluid">
					<div class="span2 mobile-span3"><p class="detail"><?=__('Provider')?></p></div>
					<div class="span10 mobile-span9"><p><?=$quote->provider->business_name?></p></div>
				</div>
				<div class="row-fluid">
					<div class="span2 mobile-span3"><p class="detail"><?=__('Quote')?></p></div>
					<div class="span10 mobile-span9"><p><?=format_currency($quote->price)?></p></div>
				</div>
				<? if ($quote->start_at): ?>
				<div class="row-fluid">
					<div class="span2 mobile-span3"><p class="detail"><?=__('Start time')?></p></div>
					<div class="span10 mobile-span9"><p><?=$quote->start_at->format('%F %H:%M %p')?></p></div>
				</div>
				<? endif ?>
				<div class="row-fluid">
					<div class="span2 mobile-span3"><p class="detail"><?=__('Note')?></p></div>
					<div class="span10 mobile-span9"><p><?=Phpr_String::show_more_link($quote->comment, 150, __('Show more', true))?></p></div>
				</div>
			</div>
			<hr />
			<div class="customer_details">
				<h4><?=__('Provide your contact details')?></h4>
				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><?= __('First name') ?></label> 
							<div class="controls">
								<input type="text" name="User[first_name]" value="<?= $this->user->first_name ?>" class="span12" />
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><?= __('Last name') ?></label> 
							<div class="controls">
								<input type="text" name="User[last_name]" value="<?= $this->user->last_name ?>" class="span12" />
							</div>
						</div>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><?= __('Phone number') ?></label> 
							<div class="controls">
								<input type="text" name="User[phone]" value="<?= $this->user->phone ?>" class="span12" />
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><?= __('Mobile phone') ?></label> 
							<div class="controls">
								<input type="text" name="User[mobile]" value="<?= $this->user->mobile ?>" class="span12" />
							</div>
						</div>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12">
						<div class="control-group">
							<label class="control-label"><?= __('Street Address') ?></label> 
							<div class="controls">
								<input type="text" name="User[street_addr]" value="<?= $this->user->street_addr ?>" class="span12" />
							</div>
						</div>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><?= __('City') ?></label> 
							<div class="controls">
								<input type="text" name="User[city]" value="<?= $this->user->city ?>" class="span12" />
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><?= __('Postal code') ?></label> 
							<div class="controls">
								<input type="text" name="User[zip]" value="<?= $this->user->zip ?>" class="span12" />
							</div>
						</div>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><? __('Country') ?></label>
							<div class="controls">
								<?=form_dropdown('User[country_id]', 
									Location_Country::get_name_list(), 
									$this->user->country_id, 
									'id="popup_quote_'.$quote->id.'_country" class="span12"', 
									 __('-- Select --', true)
								 )?>
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="control-group">
							<label class="control-label"><? __('State') ?></label>
							<div class="controls">
								<?=form_dropdown('User[state_id]', 
									Location_State::get_name_list($this->user->country_id), 
									$this->user->state_id, 
									'id="popup_quote_'.$quote->id.'_state" class="span12"', 
									__('-- Select --', true)
								)?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<p class="terms"><?=__('Accepting this quote is like a virtual handshake between you and %s. Help keep our community strong by honouring this quote.', $quote->provider->business_name)?></p>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" class="popup-close pull-left"><?=__('Review other quotes')?></a>
			<button name="done" class="btn btn-success btn-large btn-icon" id="button_quote_submit_'.$quote->id.'">
				<i class="icon-ok"></i>
				<?= __('Create Booking!') ?>
			</button>
		</div>
		<script>

			Page.requestQuotePanelFormFields = $.phpr.form().defineFields(function() {
				this.defineField('User[first_name]').required("<?=__('Please enter your first name')?>");
				this.defineField('User[last_name]').required("<?=__('Please enter your surname')?>");
				this.defineField('User[phone]').required("<?=__('Please enter your phone number')?>");
				this.defineField('User[city]').required("<?=__('Please enter your city')?>");
				this.defineField('User[zip]').required("<?=__('Please enter your zip or postal code')?>");
				this.defineField('User[country_id]').required("<?=__('Please select your country')?>");
				this.defineField('User[state_id]').required("<?=__('Please select your state')?>");
			});

		</script>
	<?=form_close()?>

</div>