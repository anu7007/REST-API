<?php

namespace MyEventsHandler;

use GuzzleHttp\Client;
use Phalcon\Di\Injectable;

class NotificationsListeners extends Injectable
{
    public function reducequantity($event, $obj, $postArr)
    {
        $url = "http://192.168.2.95:8080/frontend/products/display";

        $client = new Client();
        $client->request('POST', $url, ['form_params' => $postArr]);
    }
}
