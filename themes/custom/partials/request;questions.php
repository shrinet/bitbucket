<ul class="nav nav-pills">
	<li class="disabled"><a href="javascript:;"><?=__('Filter', true)?>:</a></li>
	<li class="active"><a href="javascript:;" data-filter-type="all"><?=__('All')?></a></li>
	<li><a href="javascript:;" data-filter-type="unanswered"><?=__('Unanswered')?></a></li>
	<li><a href="javascript:;" data-filter-type="flagged"><?=__('Flagged')?></a></li>
</ul>
<div id="request_questions_all">
	<? foreach ($request->questions as $question): ?>
		<div id="p_request_answer_form_<?=$question->id?>">
			<?=$this->display_partial('request:answer_form', array('question'=>$question))?>
		</div>
	<? endforeach ?>
</div>