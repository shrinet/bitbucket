<div class="row-fluid">
	<div class="span7 columns hide-for-small">
		<h4 class="separated"><?=__('Happening on %s now', c('site_name'))?></h5>
		<div id="p_activity_feed">
			<?=$this->display_partial('home:activity_feed')?>
		</div>
	</div>
	<div class="span5 columns">

		<div class="box">
			<div class="box-header">
				<h6><?=__('Reasons to use %s', c('site_name'))?></h6>
			</div>
			<div class="box-content">
				<ol class="usage-reasons no-bullet">
					<li><span class="label round">1</span> Connect with local service providers</li>
					<li><span class="label round">2</span> Review provider credentials online</li>
					<li><span class="label round">3</span> Save time and dramatically cut costs</li>
					<li><span class="label round">4</span> Leverage buying power</li>
					<li><span class="label round">5</span> Completely FREE to use!</li>
				</ol>
			</div>
		</div>

		<p>
			<a href="<?=root_url('provide')?>" class="btn btn-primary btn-large btn-block">
				<i class="icon-user"></i>
				<?=__('I want to provide my services', true)?> 
			</a>
		</p>

		<div class="box popular-categories">
			<div class="box-header">
				<h6><?=__('Popular categories')?></h6>
			</div>
			<div class="box-content">
				<ul class="block-grid grid-span2">
					<? foreach ($categories as $category): ?>
						<li><i class="icon-angle-right"></i> <a href="<?=Bluebell_Directory::category_url($category)?>"><?=$category->name?></a></li>
					<? endforeach ?>
				</ul>
			</div>
		</div>

		<h4><?=__('From the blog')?></h4>
		<div class="latest_blog">
			<? if ($blog_post): ?>
				<div class="title">
					<i class="icon-pencil"></i> <a href="<?=root_url('blog/post/'.$blog_post->url_title) ?>"><?=$blog_post->title?> - <span class="date"><?=$blog_post->published_at->format('%F')?><span></a>
				</div>
				<p><?=h($blog_post->description) ?> <a href="<?=root_url('blog/post/'.$blog_post->url_title) ?>" class="link-button radius"><?=__('Read more', true)?></a></p>
			<? endif ?>
		</div>
	</div>
</div>