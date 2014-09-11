<div class="well">
	<ul class="nav nav-pills" id="filter_navigation">
		<li class="active"><a href="javascript:;" class="category"><?=__('By Category')?></a></li>
		<li><a href="javascript:;" class="alpha"><?=__('Browse A-Z')?></a></li>
	</ul>
	<div class="row-fluid" id="category_form_select_category">
		<div class="span6 columns">
			<div class="control-group">
				<label for="select_parent" class="control-label"><?= __('Select a category:') ?></label>
				<div class="controls">
					<?=form_dropdown('select_parent', array(), null, 'multiple="multiple" id="select_parent"')?>
				</div>
			</div>
		</div>
		<div class="span6 columns">
			<div class="control-group">
				<label for="select_category" class="control-label"><?= __('Select a skill:') ?></label>
				<div class="controls">
					<?=form_dropdown('select_category', array(), null, 'multiple="multiple" id="select_category"')?>
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid" id="category_form_select_alpha" style="display:none">
		<div class="span6 columns">
			<div class="control-group">
				<label for="select_alpha" class="control-label"><?= __('Select a specific skill:') ?></label>
				<div class="controls">
					<?=form_dropdown('select_alpha', array(), null, 'multiple="multiple" id="select_alpha"')?>
				</div>
			</div>
		</div>
	</div>
	<p>
		<?=__('Still not finding what you are looking for?')?>
		<a href="javascript:;" id="link_suggest_category"><?=__('Suggest a new service for %s', c('site_name'))?></a>
	</p>
</div>