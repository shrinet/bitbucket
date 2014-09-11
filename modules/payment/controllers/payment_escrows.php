<?php

class Payment_Escrows extends Admin_Controller
{
    public $implement = 'Db_List_Behavior, Db_Form_Behavior';
    public $list_model_class = 'Payment_Escrow';
    public $list_record_url = null;
    public $list_record_onclick = null;

    public $list_name = null;
    public $list_custom_prepare_func = null;
    public $list_custom_body_cells = null;
    public $list_custom_head_cells = null;
    public $list_custom_partial = null;
    public $list_search_prompt = null;

    public $form_create_title = 'New Escrow';
    public $form_edit_title = 'Edit Escrow';
    public $form_model_class = 'Payment_Escrow';
    public $form_not_found_message = 'Escrow not found';
    public $form_redirect = null;

    public $form_edit_save_flash = 'Escrow has been successfully saved';
    public $form_create_save_flash = 'Escrow has been successfully added';
    public $form_edit_delete_flash = 'Escrow has been successfully deleted';

    protected $required_permissions = array('payment:manage_escrows');

    public $global_handlers = array('on_user_change', 'on_update_state_list');

    public function __construct()
    {
        parent::__construct();
        $this->app_menu = 'payment';
        $this->app_module_name = 'Payment';
        $this->app_page = 'escrows';

        $this->list_record_url = url('payment/escrows/edit');
        $this->form_redirect = url('payment/escrows');
    }

    public function index()
    {
        $this->app_page_title = 'Escrows';
    }

    public function payment_released($escrow_id)
    {
        try
        {
            $this->app_page_title = 'Payment Accepted';
            $this->view_data['form_record_id'] = $escrow_id;
            $this->view_data['escrow'] = $escrow = $this->form_find_model_object($escrow_id);
        }
        catch (Exception $ex)
        {
            $this->handle_page_error($ex);
        }
    }

    protected function on_user_change()
    {
        $form_model = $this->form_create_model_object();
        $data = post('Payment_Escrow');
        $user_id = isset($data['user_id']) ? $data['user_id'] : null;

        if (!$user_id)
            return;

        $user = User::create()->find($user_id);

        if (!$user)
            return;

        $user_fields = array(
            'first_name'=>'billing_first_name',
            'last_name'=>'billing_last_name',
            'email'=>'billing_email',
            'company'=>'billing_company',
            'city'=>'billing_city',
            'zip'=>'billing_zip',
            'state_id'=>'billing_state_id',
            'country_id'=>'billing_country_id',
        );

        foreach ($user_fields as $from_field=>$to_field)
        {
            $form_model->{$to_field} = $user->{$from_field};

            $this->prepare_partial('form_field_container_'.$to_field.'Payment_Invoice');
            $this->form_render_field_container($form_model, str_replace('_id', '', $to_field));
        }
    }

    protected function on_update_state_list()
    {

        $data = post('Payment_Invoice');

        $form_model = $this->form_create_model_object();
        $form_model->billing_country_id = $data['billing_country_id'];

        $this->prepare_partial('form_field_container_billing_state_idPayment_Invoice');
        $this->form_render_field_container($form_model, 'billing_state');
    }

    private function get_invoice_object($id)
    {
        $state = Payment_Invoice::create();
        return strlen($id) ? $state->find($id) : $state;
    }

}

