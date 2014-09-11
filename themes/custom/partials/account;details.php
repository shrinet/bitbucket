<div class="block" id="block_name">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_name',
		'title' => __('Name',true),
		'display_fields' => array(
			'name' => array('label'=>__('Your real name'))
		),
		'edit_fields' => array(
			'first_name'=> array(
				'label'=>__('First Name',true),
				'validate_rules'=>'{ required: true }', 
				'validate_messages'=>'{ required: "'.__('Please specify your first name', true).'" }'
			),
			'last_name'=> array('label'=>__('Last Name',true))
		)
	))?>
</div>

<div class="block" id="block_email">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_email',
		'title' => __('Email',true),
		'display_fields' => array(
			'email' =>array('label'=>__('Your email address'))
		),
		'edit_fields' => array(
			'email'=>array(
				'label'=>__('Email Address',true),
				'validate_rules'=>'{ required: true, email: true }', 
				'validate_messages'=>'{ 
					required: "'.__('Please specify your email address',true).'", 
					email: "'.__('Please specify a valid email address',true).'" 
				}'
			)
		)
	))?>
</div>

<div class="block" id="block_password">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_password',
		'title' => __('Password'),
		'display_fields' => array(
			'password' => array('label'=>__('Your password'), 'value'=>'**********')
		),
		'edit_fields' => array(
			'old_password'=>array(
				'label'=>__('Enter current password', true), 
				'type'=>'password', 
				'validate_rules'=>'{ 
					required: true,  
					phprRemote: {
						action: "user:on_validate_password"
					}
				}', 
				'validate_messages'=>'{ 
					required: "'.__('Please specify the old password',true).'",
					phprRemote: "'.__('Password entered is invalid',true).'"
				}'
			),
			'password'=> array(
				'label'=>__('Create a Password', true), 
				'type'=>'password', 'validate_rules'=>'{ required: true }', 
				'validate_messages'=>'{ required: "'.__('Please specify a new password',true).'" }'
			),
			'password_confirm'=> array(
				'label'=>__('Confirm Password', true), 
				'type'=>'password', 
				'validate_rules'=>'{ required: true, equalTo:"#password" }', 
				'validate_messages'=>'{ 
					required: "'.__('Please specify a new password',true).'", 
					equalTo: "'.__('Password and confirmation password do not match.',true).'" 
				}'
			)
		)
	))?>
</div>

<div class="block" id="block_address">
	<?=$this->display_partial('account:detail_block',  array(
		'block_id' => 'block_address',
		'title' => __('Contact Details'),
		'display_fields' => array(
			'phone' =>array('label'=>__('Your contact phone')),
			'mobile' => array('label'=>__('Your mobile phone')),
			'address_string' => array('label'=>__('Your address'))

		),
		'edit_fields' => array(
			'phone'=> array('label'=>__('Phone number', true) ),
			'mobile'=> array('label'=>__('Mobile number', true)),
			'street_addr'=> array('label'=>__('Address', true)),
			'city'=> array('label'=>__('City', true), 'align'=>'left'),
			'zip'=> array('label'=>__('Zip / Postal Code', true), 'align'=>'right'),
			'country_id'=> array('label'=>__('Country'), 'type'=>'country', 'align'=>'left'),
			'state_id'=> array('label'=>__('State'), 'type'=>'state', 'align'=>'right'),
		)
	))?>
</div>
