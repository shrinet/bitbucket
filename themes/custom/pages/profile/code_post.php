<?
$this->data['ratings'] = $ratings = $provider->find_ratings();
$this->data['pagination'] = $ratings->paginate($this->request_param(1, 1)-1, 3);








?>