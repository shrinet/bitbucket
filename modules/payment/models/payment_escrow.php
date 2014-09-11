<?php

    class Payment_Escrow extends Db_ActiveRecord
    {
        public $table_name = 'payment_escrow';

       # public $implement = 'Db_Model_Dynamic, Db_Model_Log';

        #protected $api_added_columns = array();

        const status_unpaid = 'Unpaid';
        const status_funded = 'Escrow Funded';
        const status_released = 'Escrow Released';

        public $belongs_to = array(
            'user' => array('class_name' => 'User', 'foreign_key' => 'user_id'),
            'provider' => array('class_name' => 'Service_Provider', 'foreign_key' => 'provider_id'),
            'request' => array('class_name' => 'Service_Request', 'foreign_key' => 'request_id'),
            'quote' => array('class_name' => 'Service_Quote', 'foreign_key' =>'quote_id'),
            'invoice' => array('class_name' => 'payment_invoice', 'foreign_key' => 'invoice_id')
        );

        public function define_columns($context = null)
        {
            $this->define_column('id', '#');
            $this->define_column('created_at', 'Created')->default_invisible();
            $this->define_column('updated_at', 'Last Updated')->default_invisible();
            $this->define_relation_column('request', 'request', 'Request', db_varchar, "@title")->validation()->required('Quote must have a Request');
            $this->define_relation_column('user', 'user', 'User', db_varchar, '@username')->validation()->required('Quote must have a User');
            $this->define_relation_column('provider', 'provider', 'Provider', db_varchar, "@id")->validation();
            $this->define_relation_column('invoice', 'invoice', 'Invoice', db_varchar, "@id")->default_invisible()->validation();
            $this->define_relation_column('quote', 'quote', 'Quote', db_varchar, "@id")->default_invisible()->validation();
            $this->define_column('escrow_status', 'Status');
            $this->define_column('total_price', 'Total Price')->currency(true);
            $this->define_column('commission', 'Commission')->currency(true);
            $this->define_column('transaction', 'Transaction')->default_invisible();
						$this->define_column('funded_at', 'Funded by Client')->time_format('%I:%M %p')->date_as_is();
            $this->define_column('released_at', 'Released to Provider')->time_format('%I:%M %p')->date_as_is();
            $this->define_column('is_requested', 'Request Release' ,db_bool);

            // Extensibility
            $this->defined_column_list = array();
            $this->api_added_columns = array_keys($this->defined_column_list);
        }


        public function define_form_fields($context = null)
        {
            $this->add_form_field('user','left')
                ->display_as(frm_record_finder, array(
                    'sorting'=>'first_name, last_name, email',
                    'list_columns'=>'first_name,last_name,email',
                    'search_prompt'=>'Find user by name or email',
                    'form_title'=>'Find User',
                    'display_name_field'=>'first_name',
                    'display_description_field'=>'email',
                    'prompt'=>'Click Find to find a user'))->tab('Quote');

            $this->add_form_field('provider','right')
                ->display_as(frm_record_finder, array(
                    'sorting'=>'business_name',
                    'list_columns'=>'business_name',
                    'search_prompt'=>'Find provider by business name',
                    'form_title'=>'Find Provider',
                    'display_name_field'=>'business_name',
                    'display_description_field'=>'id',
                    'prompt'=>'Click Find to find a provider'))->tab('Quote');
            $this->add_form_field('escrow_status', 'left')->tab('Quote');
            $this->add_form_field('funded_at','left')->tab('Quote');
            $this->add_form_field('released_at','left')->tab('Quote');
            #$this->add_form_field('duration','right')->tab('Quote');
            #$this->add_form_field('price','left')->tab('Quote');
            #$this->add_form_field('comment','full')->tab('Quote');

            // Extensibility

        }

        public function before_create($session_key = null)
        {
            $this->set_status(self::status_unpaid);
        }


        // Options
        //

        public function get_type_options($key_value = -1)
        {
            $options = array(
                self::status_unpaid => 'Unpaid',
                self::status_funded => 'Escrow Funded',
                self::status_released => 'Escrow Released'
            );

            if ($key_value == -1)
                return $options;
            else if (array_key_exists($key_value, $options))
                return $options[$key_value];
            else
                return '???';
        }


        public function apply_status()
        {
            $this->where('type=:Unpaid OR type=:Escrow Funded OR type=:Escrow Released', array(
                'Unpaid' => self::status_unpaid,
                'Escrow Funded' => self::status_funded,
                'Escrow Released' => self::status_released
            ));
            return $this;
        }

        public function set_status($status)
        {
            if(!$status)
                return $this;
            $this->escrow_status = $status;
            return $this;
        }

        public static function create_escrow($quote = null, $invoice = null, $commission)
        {
            $escrow = self::create();
            $escrow->copy_from_quote($quote);
            $escrow->invoice_id = $invoice->id;
            $escrow->total_price = $invoice->total;
            $escrow->commission = $commission;


            return $escrow;
        }
				
				public function escrow_funded($transaction_id)
        {
            $id = $this->id;
            $this->set_status(self::status_funded);
            $this->save();
            $bind = array(
                'id'=> $id,
                'transaction_id' =>$transaction_id,
                'funded_at' => Phpr_DateTime::now()->to_sql_datetime()
            );

            Db_Helper::query('update payment_escrow set transaction=:transaction_id, funded_at=:funded_at where id=:id', $bind);
            return;
        }

        public function copy_from_quote($quote)
        {
            $this->quote_id = $quote->id;
            $this->request_id = $quote->request_id;
            $this->provider_id = $quote->user->id;
            $this->user_id = $quote->request->user->id;
        }
				
				public function set_requested()
        {
            $bind = array(
                'id' => $this->id,
                'is_requested'=>true
            );
            Db_Helper::query('update payment_escrow set is_requested=:is_requested where id=:id', $bind);
            return;
        }

        public function request_release()
        {
            Payment_Actions::request_release($this);
            return;
        }

        public static function find_by_invoice_id($invoiceId)
        {
            $escrow = Db_DbHelper::scalar('select * from payment_escrow where invoice_id=:id', array('id'=>$invoiceId));
            return $escrow;

        }


    }