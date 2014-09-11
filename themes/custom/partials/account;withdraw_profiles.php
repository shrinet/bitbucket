<div class="block" id="block_ssn">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_ssn',
		'title' => __('SSN',true),
		'display_fields' => array(
			'ssn' => array('label'=>__('Social Security Number'))
		),
		'edit_fields' => array(
			'ssn'=> array(
				'label'=>__('Social Security Number',true),
				'validate_rules'=>'{ required: true }', 
				'validate_messages'=>'{ required: "'.__('Please specify Social Security Number', true).'" }'
			),
		)
	))?>
</div>
<div class="block" id="block_femail">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_femail',
		'title' => __('Funding Email',true),
		'display_fields' => array(
			'funding_email' => array('label'=>__('Your Funding Email'))
		),
		'edit_fields' => array(
			'funding_email'=> array(
				'label'=>__('Funding Email',true),
				'validate_rules'=>'{ required: true }', 
				'validate_messages'=>'{ required: "'.__('Please specify your Funding Email', true).'" }'
			),
		)
	))?>
</div>
<div class="block" id="block_accn">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_accn',
		'title' => __('Account No',true),
		'display_fields' => array(
			'account_no' => array('label'=>__('Your Bank Account Number'))
		),
		'edit_fields' => array(
			'account_no'=> array(
				'label'=>__('Account no#',true),
				'validate_rules'=>'{ required: false }', 
				'validate_messages'=>'{ required: "'.__('Please specify your first name', true).'" }'
			),
		)
	))?>
</div>
<div class="block" id="block_routn">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_rountn',
		'title' => __('Rounting No',true),
		'display_fields' => array(
			'routing_no' => array('label'=>__('Your Bank Routing Number'))
		),
		'edit_fields' => array(
			'routing_no'=> array(
				'label'=>__('Your Bank Routing Number',true),
				'validate_rules'=>'{ required: false }', 
				'validate_messages'=>'{ required: "'.__('Please specify your first name', true).'" }'
			),
		)
	))?>
</div>
<div class="block" id="block_txid">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_txid',
		'title' => __('Tax ID',true),
		'display_fields' => array(
			'tax_id' => array('label'=>__('Your Tax ID'))
		),
		'edit_fields' => array(
			'tax_id'=> array(
				'label'=>__('Tax ID',true),
				'validate_rules'=>'{ required: false }', 
				'validate_messages'=>'{ required: "'.__('Please specify your first name', true).'" }'
			),
		)
	))?>
</div>