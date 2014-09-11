<? if ($activity): ?>
	<ul class="small-block-grid-1">
		<? foreach ($activity as $key=>$feed_item): ?>
			<? 
				if (!$feed_item->user) 
					continue;
				
				$is_last = !($key==($activity->count - 1));
			?>
			<li class="item">
				<div class="<?=$is_last?'separator bottom':''?>">
					<?
						$class_name = get_class($feed_item);
					?>
					<? if ($class_name == "Service_Request"): ?>
						<div class="avatar"><img src="<?=Bluebell_User::avatar($feed_item->user)?>" alt="" /></div>
						<div class="title">
							<a href="<?=$feed_item->get_url('job')?>"><?=$feed_item->user->username?></a> 
							<small><?=__('has a new request')?></small>
						</div>
						<div class="description">
							<i class="icon-bullhorn"></i>
							<strong><?=$feed_item->title?></strong> 
							<?=Phpr_String::limit_words($feed_item->description, 20)?>
						</div>
						<div class="info"><?=__('%s ago', Phpr_DateTime::interval_to_now($feed_item->created_at), true)?> – <?=Bluebell_Request::location($feed_item, true)?></div>    
					<? elseif ($class_name == "Service_Provider"): ?>
						<div class="avatar"><img src="<?=Bluebell_Provider::avatar($feed_item)?>" alt="" /></div>
						<div class="title">
							<a href="<?=$feed_item->get_url('profile')?>"><?=$feed_item->business_name?></a> 
							<small><?=__('has a new service')?></small>
						</div>
						<div class="description">
							<i class="icon-user"></i>
							<strong><?=$feed_item->role_name?></strong> 
							<?=Phpr_String::limit_words($feed_item->description, 20)?>
						</div>
						<div class="info"><?=__('%s ago', Phpr_DateTime::interval_to_now($feed_item->created_at), true)?> – <?=$feed_item->location_string?></div>
					<? endif ?>
				</div>
			</li>
		<? endforeach ?>
	</ul>
<? else: ?>
	<p>No activity here yet! Check back again soon...</p>
<? endif ?>