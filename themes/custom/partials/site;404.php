<?

	$error_message = (isset($error_message)) ? $error_message : __('Page not found', true);

?>
<div class="page-header">
	<h1><?=$error_message?></h1>
	<h4 class="subheader"><?=__('Something went wrong and we could not find what you were looking for...', true)?></h4>
</div>

<?=global_content_block('page_not_found', 'Page not found')?>
