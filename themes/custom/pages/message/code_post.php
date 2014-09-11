<?

$this->data['related_request'] = null;

if ($message && $message->master_object_class == 'Service_Quote')
{
    $quote = Service_Quote::create()->find($message->master_object_id);
    if ($quote)
    {
        $this->data['related_request'] = $quote->request;
    }
}

?>