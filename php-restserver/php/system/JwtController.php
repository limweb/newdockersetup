<?php
require_once __DIR__.'/BaseController.php';
class JwtController extends BaseController
{

    public $member = null;

    public function authorize()
    {
        try {
            $token = \Request::getInstance()->token;
            $jwt = new JwtService();
            // dump('token-->', $token);
            $rs = $jwt->verify($token);
            // dump($rs);
            if ($rs) {
                $this->member = $jwt->getMember($token);
                $this->server->setStatus(200);
                return true;
            } else {
                $this->server->setStatus(401);
                return false;
            }
        } catch (Exception $e) {
            $this->server->setStatus(401);
            return false;
        }
    }
}
