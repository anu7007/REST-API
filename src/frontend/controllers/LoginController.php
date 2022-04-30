<?php


use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {
        if ($this->request->getPost('login')) {
            $data = array(
                "email" => $this->request->getPost('email'),
                "password" => $this->request->getPost('password')
            );
            $users = $this->mongo->users->findOne(["email" => $data['email'], "password" => $data['password']]);
            if (empty($data['email']) || empty($data['password'])) {
                $this->view->loginmsg = "<span class='text text-danger'>*Please fill all fields.</span>";
            } elseif (!$users) {
                $this->view->loginmsg = "<span class='text text-danger'>User not exists.</span>";
            } else {
                $this->response->redirect('/app/orders/index');
            }
        }
    }
}