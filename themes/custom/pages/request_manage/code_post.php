<?
if (!$request)
    return;

if ($request->status_code == Bluebell_Request::status_booked || $request->status_code == Service_Status::status_closed || $request->status_code == Bluebell_Request::status_booked_cancelled)
    Phpr::$response->redirect($request->get_url('job/booking'));

$show_quotes = $show_questions = $can_edit = false;

if ($request->status_code == Service_Status::status_active || $request->status_code == Service_Status::status_expired)
    $show_quotes = $show_questions = $can_edit = true;

if ($request->quotes->count == 0)
    $show_quotes = false;

if ($request->questions->count == 0)
    $show_questions = false;

$this->data['show_quotes'] = $show_quotes;
$this->data['can_edit'] = $can_edit;
$this->data['show_questions'] = $show_questions;




?>