<?
	$can_ask = (isset($can_ask)) ? $can_ask : true;
	if (!$this->user) $can_ask = false;    
?>
<? if ($questions->count): ?>
	<h5><?=__('Job Questions')?></h5>
	<ul id="job_questions">
		<? foreach ($questions as $question): ?>
		<li>
			<? if ($question->answer): ?>
				<a href="javascript:;" class="question"><?=$question->description?></a>
				<span class="answer"><?=$question->answer->description?></span>
			<? else: ?>
				<span class="question"><?=$question->description?></span>
			<? endif ?>
		</li>
		<? endforeach ?>
	</ul>
<? endif ?>

<? if ($can_ask): ?>
	<p><a href="javascript:;" id="link_ask_question" class="btn btn-primary btn-block"><?=__('Ask a question')?></a></p>
	<div id="ask_question" style="display:none">
		<?=form_open(array('id'=>'ask_question_form'))?>

			<input type="hidden" name="request_id" value="<?= $request->id ?>" />
			<input type="hidden" name="Question[is_public]" value="1" />

			<div class="control-group">
				<div class="controls">
					<textarea name="Question[description]" class="span12" placeholder="<?= __("Enter your question here") ?>"></textarea>
				</div>
			</div>
			<?=form_submit('ask', __('Submit', true), 'class="btn btn-success btn-block"')?>

		<?=form_close()?>
	</div>

	<script>
		Page.askQuestionFormFields = $.phpr.form().defineFields(function(){
			this.defineField('Question[description]', 'Question Description').required("<?=__('Please enter your question')?>")
		});
	</script>    
<? endif ?>