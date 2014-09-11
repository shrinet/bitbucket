<?
	$bookings = Bluebell_Request::get_bookings($this->user)->find_all();
?>
<div class="well">
	<h5><?=__('Upcoming Appointments')?></h5>
	<ul class="square">
		<? if (count($bookings)): ?>
			<? foreach ($bookings as $booking): ?>
				<li>
					<a href="<?=$booking->get_url('job/booking')?>">
						<?=__('%s Request', '<strong>'.$booking->title.'</strong>', true)?> - 
						<?=Bluebell_Request::location($booking)?>
					</a>
				</li>
			<? endforeach ?>
		<? else: ?>
			<li><?=__('You have no upcoming appointments',true)?></li>
		<? endif ?>
	</ul>
</div>