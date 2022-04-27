<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SignupController extends Controller
{
    public function indexAction()
    {
        if($this->request->getPost('register')) {
            $data = [];
            $data = array(
                "name" => $this->request->getPost('name'),
                "email" => $this->request->getPost('email'),
                "password" => $this->request->getPost('password')
            );
            $this->mongo->users->insertOne($data);
            $user = $this->mongo->users->findOne(['email'=>$data['email']]);
            $id = strval($user->_id);

            $token = $this->token($id);
            echo $token;
            die;
        }
    }

    public function token($id)
    {
        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "exp" => time() * 24 + 3600,
            "role" => "user",
            "id" => $id
        );

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }
}