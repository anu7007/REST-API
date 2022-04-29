<?php

namespace Api\Handlers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Http\Response;

use Phalcon\Di\Injectable;

class Order extends Injectable
{
    function placeorder()
    {
        $token = $this->request->getQuery('token');
        $key = 'example_key';
        $decodedtoken = JWT::decode($token, new Key($key, 'HS256'));
        $response = new Response();
        $bodyData = $this->request->getPost();
        $orderData = $this->mongo->order->find()->toArray();
        $orderCount = count($orderData);

        if (isset($bodyData['customer_name']) && isset($bodyData['product_id']) && isset($bodyData['product_name']) && isset($bodyData['quantity'])) {
            $data = array(
                "order_id" => strval($orderCount + 1),
                "customer_id" => $decodedtoken->id,
                "customer_name" => $this->request->getPost('customer_name'),
                "product_id" => $this->request->getPost('product_id'),
                "product_name" => $this->request->getPost('product_name'),
                "product_quantity" => $this->request->getPost('quantity'),
                "status" => "paid"
            );
            $products = $this->mongo->products->findOne(['id' => $data['product_id']]);
            if (isset($products)) {
                $this->mongo->order->insertOne($data);
            } else {
                $data = ["status" => 404, "data" => "product not found"];
                return $response->setJSONContent($data)->send();
            }
        } else {
            $data = ["status" => 404, "data" => "undefined data format"];
            return $response->setJSONContent($data)->send();
        }
    }

    function updateorder()
    {
        $response = new Response();
        if ($this->request->isPut()) {

            $updated_status = $this->request->getPut('status');
            $id = $this->request->getPut('id');
            // $this->mongo->order->find()->toArray()
            $orders = $this->mongo->order->findOne(['order_id' => $id]);
            // die($orders);
            if ($orders) {
                $this->mongo->order->updateOne(["order_id" => $id], ['$set' => ['status' => $updated_status]]);
            } else {
                $data = ["status" => 404, "data" => "order does not exist"];
                return $response->setJSONContent($data)->send();
            }
        }
    }
}
