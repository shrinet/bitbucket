<?php

class Blog_Category_Menu_Item extends Cms_Menu_Item_Base
{
    public function get_info()
    {
        return array(
            'name'=>'Blog Category Link',
            'description'=>'Links to a Blog Category page'
        );
    }

    public function build_config_form($host)
    {
        $host->add_field('blog_category_id', 'Blog Category', 'full', db_number, 'Link')
            ->comment('Please select the Blog Category to link to', 'above')
            ->display_as(frm_radio)
            ->validation()->required('Please select the Blog Category to link to');
    }

    public function build_menu_item($host)
    {
        $category = Blog_Category::create()->find($host->blog_category_id);
        
        if (!$category)
            throw new Phpr_ApplicationException('Blog category not found: '. $host->blog_category_id);

        $host->label = $category->name;
        $host->url = $category->url_name;
    }

    public function get_blog_category_id_options($key_value= -1)
    {
        $categories = Blog_Category::create()->find_all()->as_array('name', 'id');
        return $categories;
    }

    public function get_blog_category_id_option_state($key_value= -1)
    {
        return false;
    }    

}