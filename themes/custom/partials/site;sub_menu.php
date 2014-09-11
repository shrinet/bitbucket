<?
	$categories = Service_Category::get_popular_categories()->where('parent_id is null')->limit(6)->find_all();
?>
<div class="tabs-below">
	<ul class="nav nav-tabs">

		<? foreach ($categories as $category): ?>
		<?	
			$children = $category->list_children();
			$has_children = count($children);
		?>
			<li class="<?=$has_children?'dropdown':''?>">
				<a href="<?=root_url('directory/b/'.$category->url_name)?>" class="dropdown-toggle" data-toggle="dropdown">
					<?=$category->name?> 
					<?=$has_children ? '<b class="caret"></b>' : '' ?>
				</a>

				<? if ($has_children): ?>
					<ul class="dropdown-menu">
						<? foreach ($children as $role): ?>
							<li><a href="<?=root_url('directory/b/'.$category->url_name.'/'.$role->url_name)?>"><?=$role->name?></a></li>
						<? endforeach ?>
					</ul>
				<? endif ?>
			</li>
		<? endforeach ?>
		<li>
			<a href="<?=root_url('directory/browse')?>"><?=__('More')?>&hellip;</a>
		</li>
	</ul>
</div>