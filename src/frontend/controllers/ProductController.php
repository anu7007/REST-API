<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;

class ProductController extends Controller
{
    public function indexAction()
    {
        $data = $this->mongo->product->find();
        $this->view->product = $data;
    }
    public function placeorderAction()
    {
        $id = $this->request->getPost('id');
        $this->view->id = $id;
    }
    public function placeorderrAction()
    {
        if ($this->request->getPost('order')) {
            $data = array(
                'customer_name' => $this->request->getPost('customer_name'),
                'product_id' => $this->request->getPost('product_id'),
                'product_name' => $this->request->getPost('product_name'),
                'quantity' => $this->request->getPost('quantity')
            );
            // print_r($data);
            // die;
            $token = $this->token;
            // die($token);
            $url = 'http://192.168.2.95:8080/api/order/placeorder/?token=' . $token;
            $client = new Client();
            $client->request('POST', $url, ['form_params' => $data]);
            $this->response->redirect("/frontend/product/index");
        }
    }
    public function displayAction()
    {
        $post = $this->request->getPost();
        $id = $post['id'];
        $quantity = $post['stock'];
        $this->mongo->products->updateOne(["id" => $id], ['$set' => ['stock' => $quantity]]);
    }
}
