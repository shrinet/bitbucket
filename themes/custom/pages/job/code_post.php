<?

if ($request && $this->user && $request->user_id == $this->user->id)
    Phpr::$response->redirect($request->get_url('request/manage'));

if ($request && $quote && $quote->status->code == Service_Quote_Status::status_accepted)
    Phpr::$response->redirect($request->get_url('job/booking'));

?>