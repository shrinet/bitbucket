<?php

class Bluebell_Directory
{
	public static function category_url($category)
	{
		if (!$category)
			return root_url('directory/browse');
		else if ($category->parent)
			return root_url('directory/b/'.$category->parent->url_name.'/'.$category->url_name);
		else
			return root_url('directory/b/'.$category->url_name);
	}
}