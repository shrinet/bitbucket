<? if (!strlen($provider->description)): ?>
	<p id="button_profile_description" class="form-field"><?=__('Click to add a short description about your business')?></p>
<? else: ?>
<div id="button_profile_description" class="form-field">
	<p><?=$provider->description_html?></p>
</div>
<? endif ?>