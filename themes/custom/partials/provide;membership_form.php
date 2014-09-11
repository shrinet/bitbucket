<h4>Membership</h4>
<?
$has_membership = $provider->plan_id;

if($has_membership) {
	$pops = Service_Plan::get_object_list();
	$plan = $pops[$has_membership];
?>
	<div class="planPanel">
		<h4 class="planHeading">Plan Details</h4>
		<div class="planTitle"><span>Your Plan: </span><? echo $plan->name; ?> membership (month to month)</div>
		<div class="planDate"><span>Renewal Date: </span></div>
	</div>
	<a href="javascript:;" id="link_update_membership"><?=__('Change/Update Membership')?></a>
<? } else { ?>
	<a href="javascript:;" id="link_update_membership"><?=__('Select Membership')?></a>
<? } ?>