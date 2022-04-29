<?php

use Phalcon\Mvc\Controller;

class OrdersController extends Controller
{
    public function indexAction()
    {
        $data = $this->mongo->order->find()->toArray();
        $this->view->orders = $data;
    }
}