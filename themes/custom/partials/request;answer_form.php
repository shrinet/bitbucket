<?
	$question = (isset($question)) ? $question : null;
?>
<? if ($question): ?>
<?
	if ($question->answer)
		$status = "answered";
	else if ($question->is_public)
		$status = "unanswered";
	else
		$status = "flagged";
?>
<div class="answer_form <?=$status?>">

	<?=form_open(array('id'=>'form_answer_question_'.$question->id))?>
		<div class="row-fluid">
			<div class="span11">
				<input type="hidden" name="request_id" value="<?= $request->id ?>" />
				<input type="hidden" name="question_id" value="<?= $question->id ?>" />
				<input type="hidden" name="Answer[is_public]" value="1" />

				<!-- Question -->
				<h5><?=$question->description?></h5>
				<div class="author"><?=__('Posted by', true)?> <?=($question->provider) ? $question->provider->business_name : __('Unknown', true) ?></div>

				<!-- Answer -->
				<? if ($status == "answered"): ?>
					<?=form_hidden('answer_id', $question->answer->id)?>
					<div class="view">
						<p><?=$question->answer->description?></p>
					</div>
					<div class="edit" style="display:none">
						<div class="control-group">
							<?=form_textarea('Answer[description]', $question->answer->description, 'class="span12" placeholder="'.__("Enter your answer here").'"')?>
						</div>
						<?=form_submit('answer', __('Update answer'), 'class="btn btn-success"')?>
						<div class="terms"><?=__('Sharing your contact details is against our terms and conditions')?></div>
					</div>
				<? elseif ($status == "unanswered"): ?>
					<div class="control-group">
						<?=form_textarea('Answer[description]', '', 'class="span12" placeholder="'.__("Enter your answer here").'"')?>
					</div>
					<?=form_submit('answer', __('Answer', true), 'class="btn"')?>
					<div class="terms"><?=__('Sharing your contact details is against our terms and conditions')?></div>
				<? else: ?>
					<p><span class="label label-important"><?=__('You have flagged this question as inappropriate')?></span></p>
				<? endif ?>
			</div>
			<div class="span1 controls">
				<? if ($status=="answered"): ?>
					<a href="javascript:;" onclick="Page.questionEditToggle(<?=$question->id?>)">Edit</a>
				<? elseif ($status=="unanswered"): ?>
					<a href="javascript:;" onclick="Page.questionFlag(<?=$question->id?>)">Flag</a>
				<? else: ?>
					<a href="javascript:;" onclick="Page.questionFlag(<?=$question->id?>, true)">Unflag</a>
				<? endif ?>
			</div>
		</div>
	<?=form_close()?>

</div>

<script>

Page.requestAnswerFormFields = $.phpr.form().defineFields(function() {
	this.defineField('Answer[description]').required("<?=__('Please enter an answer to this question')?>");
});
Page.questionValidateForm(<?=$question->id?>, '<?=$status?>');

</script>

<? endif ?>