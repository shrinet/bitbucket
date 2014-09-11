<?php namespace Db;

class Html_Widget extends Form_Widget_Base
{

	public $field_name = null;
	public $field_id = null;
	public $field_value = null;
	public $css_class = null;

	protected function load_resources()
	{
		$this->controller->add_javascript($this->get_public_asset_path('javascript/redactor.js?'.module_build('core')));
		$this->controller->add_css($this->get_public_asset_path('css/redactor.css?'.module_build('core')));
	}

	public function render()
	{
		if (!isset($this->field_name)) 
			$this->field_name = $this->model_class.'['.$this->column_name.']';
		
		if (!isset($this->field_id)) 
			$this->field_id = "id_".strtolower($this->model_class)."_".strtolower($this->column_name);

		$this->display_partial('html_container');
	}

}