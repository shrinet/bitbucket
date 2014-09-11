<?

	$object = $this->user;

	if ($block_key = post('detail_block_key'))
	{
		$object = User::create()->find($this->user->id);
		$block_key = unserialize(base64_decode($block_key));
		extract($block_key);
	}
	
	if (!isset($title))
		$title = "Unknown";

	if (!isset($edit_fields))
		$edit_fields = array();

	if (!isset($display_fields))
		$display_fields = $edit_fields;

	$block_key = base64_encode(serialize(array(
		'block_id'=>$block_id,
		'title'=>$title, 
		'edit_fields'=>$edit_fields, 
		'display_fields'=>$display_fields
	)));

?>
<div class="block">
	<div class="edit-link">
		<a href="javascript:;" onclick="Page.toggleEdit(this)" class="label label-important">Edit</a>
	</div>
	<h5 class="block-title"><?=$title?></h5>
	<div class="row-fluid view">
		<div class="span12">
			<? foreach ($display_fields as $name => $field): ?>
				<?
					$field = (object)$field;
					$value = isset($field->value) 
						? $field->value 
						: (strlen($object->$name) 
							? $object->$name 
							: '&nbsp;');
				?>
				<div class="block-summary"><?=$field->label?></div>
				<div class="block-value"><?=$value?></div>
			<? endforeach ?>
		</div>
	</div>
	<div class="row-fluid edit" style="display:none">
		<div class="span7">
			
			<?=form_open(array('id'=>'form_'.$block_id))?>
			<input type="hidden" name="detail_block_key" value="<?= $block_key ?>" />
			
				<? foreach ($edit_fields as $name => $field): ?>
					<?
						$field = (object)$field;
						$field_name = "User[".$name."]";

						if (!isset($field->type))
							$field->type = null;

						$align = isset($field->align) ? $field->align : null;
						$class = ($align) ? 'span6' : 'span12';

					?>				
				<? if ($align != "right") { ?><div class="row-fluid"><? } ?>
					<div class="control-group <?=$name?> <?=$class?>">
						<label for="<?=$name?>" class="control-label"><?=$field->label?></label>
						<div class="controls">
							<?
								switch ($field->type)
								{
									default: echo form_input($field_name, $object->$name, 'id="'.$name.'" class="span12"'); break;
									case "country": 
										echo form_dropdown($field_name, Location_Country::get_name_list(), $object->$name, 'id="'.$name.'" class="span12"',  __('-- Select --', true)); 
										echo "<script> jQuery(document).ready(function() { Page.initCountrySelect('#".$name."'); }); </script>";
									break;
									case "state": echo form_dropdown($field_name, Location_State::get_name_list($object->country_id), $object->$name, 'class="span12"', __('-- Select --', true)); break;
									case "password": echo form_password($field_name, '', 'id="'.$name.'" class="span12"'); break;
								}
							?>
						</div>
					</div>
				<? if ($align != "left") { ?></div><? } ?>
				<? endforeach ?>
				<div class="row-fluid control_panel">
					<div class="span12">
						<?=form_submit('submit', __('Save', true), 'class="btn btn-primary"')?> 
						&nbsp; <a href="javascript:;" class="cancel-text" onclick="Page.toggleEdit(this)"><?=__('Cancel', true)?></a>
					</div>
				</div>
			<?=form_close()?>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($) { 

		// Throw-away var
		var accountDetailBlockFormFields = $('#form_<?=$block_id?>').phpr().form();
		accountDetailBlockFormFields.defineFields(function(){
			<? foreach ($edit_fields as $name => $field): ?>
			<? 
				$field = (object)$field; 
				$field_name = "User[".$name."]";
			?>
			<? if (isset($field->validate_rules) && isset($field->validate_messages)): ?>
				this.defineField('<?=$field_name?>')
					.setRules(<?=$field->validate_rules?>)
					.setMessages(<?=$field->validate_messages?>);
			<? endif ?>
			<? endforeach ?>
		});
		accountDetailBlockFormFields.validate()
			.action('user:on_update_account')
			.beforeSend(function(){ Page.toggleEdit($('#form_<?=$block_id?>')); })
			.update('#<?=$block_id?>', 'account:detail_block');
	});

</script>