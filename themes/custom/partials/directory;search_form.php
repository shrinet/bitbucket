<?
	$placeholder = isset($placeholder) ? $placeholder : '';
?>
<div class="control-group">
	<div class="controls">
		<div class="input-append span12">
			<input type="text" id="address" 
				name="address" 
				class="span10"
				placeholder="<?=$placeholder?>">
			<?=form_submit('submit', __('Search'), 'class="btn"')?>
		</div>
	</div>
</div>

<div class="clearfix"></div>

<script>

Page.directorySearchFormFields = $.phpr.form().defineFields(function(){
	this.defineField('address', 'Address').required("<?=__('Please provide a valid location',true)?>").action('location:on_validate_address', "<?=__('Please provide a valid location', true)?>");
});

</script>