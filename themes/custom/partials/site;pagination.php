<?

$current_page_index = $pagination->get_current_page_index();
$page_number = $pagination->get_page_count();
$suffix = isset($suffix) ? $suffix : null;
$base_url = isset($base_url) ? $base_url : null;

// Group anything over 10 pages
$start_index = $current_page_index-5;
$end_index = $current_page_index+5;
$last_page_index = $page_number-1;

if ($start_index < 0)
	$start_index = 0;

if ($end_index > $last_page_index)
	$end_index = $last_page_index;

if (($end_index - $start_index) < 11)
	$end_index = $start_index + 11;

if ($end_index > $last_page_index)
	$end_index = $last_page_index;

if (($end_index - $start_index) < 11)
	$start_index = $end_index - 11;

if ($start_index < 0)
	$start_index = 0;

?>
<div class="pagination">
	<ul>
		<? if ($current_page_index): ?>
			<li class="arrow"><a href="<?=($base_url) ? $base_url.'/'.($current_page_index).$suffix : 'javascript:;'?>" data-page="<?=$current_page_index?>">&laquo;</a></li>
		<? else: ?>
			<li class="arrow disabled"><a href="">&laquo;</a></li>    
		<? endif ?>

		<? if ($page_number < 11): ?>
			
			<? for ($i = 1; $i <= $page_number; $i++): ?>
				<? if ($i != $current_page_index+1): ?>
					<li><a href="<?=($base_url) ? $base_url.'/'.($i).$suffix : 'javascript:;'?>" data-page="<?=$i?>"><?=$i?></a></li>
				<? else: ?>
					<li class="active"><a href=""><?=$i?></a></li>
				<? endif ?>
			<? endfor ?>

		<? else: ?>

			<? if ($start_index > 0): ?>
				<li><a href="<?=($base_url) ? $base_url.'/1'.$suffix : 'javascript:;'?>" data-page="1">1</a></li>
				<? if ($start_index > 1): ?><li class="disabled"><a href="">&hellip;</a></li><? endif ?>
			<? endif ?>

			<? for ($i = $start_index+1; $i <= $end_index+1; $i++): ?>
				<? if ($i != $current_page_index+1): ?>
					<li><a href="<?=($base_url) ? $base_url.'/'.($i).$suffix : 'javascript:;'?>" data-page="<?=$i?>"><?=$i?></a></li>
				<? else: ?>
					<li class="active"><a href=""><?=$i?></a></li>
				<? endif ?>
			<? endfor ?>

			<? if ($end_index < $last_page_index): ?>
				<? if ($last_page_index - $end_index > 0): ?><li class="disabled"><a href="">&hellip;</a></li><? endif ?>
				<li><a href="<?=($base_url) ? $base_url.'/'.($last_page_index+1).$suffix : 'javascript:;'?>" data-page="<?=($last_page_index+1)?>"><?=($last_page_index+1)?></a></li>
			<? endif ?>
			
		<? endif ?>

		<? if ($current_page_index < $page_number-1): ?>
			<li class="arrow"><a href="<?=($base_url) ? $base_url.'/'.($current_page_index+2).$suffix : 'javascript:;'?>" data-page="<?=($current_page_index+2)?>">&raquo;</a></li>
		<? else:?>
			<li class="arrow disabled"><a href="">&raquo;</a></li>
		<? endif ?>    
	</ul>
</div>
<div>
	<?=__('<span class="interval">Showing <strong>%s - %s</strong> of </span><strong class="row_count">%s</strong> records', array(
		($pagination->get_first_page_row_index()+1),
		($pagination->get_last_page_row_index()+1),
		$pagination->get_row_count()
	), true)?>
</div>