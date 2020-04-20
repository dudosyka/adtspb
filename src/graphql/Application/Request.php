<?php

use \GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\User;

class Request
{
    public static function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    public static function getRequestHeaderValue($header){
        return self::getRequestHeaders()[$header] ?: null;
    }

    /**
     * @param string $bearer_token
     * @return mixed
     * @throws Exception
     */
    public static function getSessionUserIDFromBearer(string $bearer_token){

        $token = Bearer::parseBearer($bearer_token);
        $user_id = $token->getClaim("uid");
        if($user_id == "" || $user_id == null || !isset($user_id)){
            throw new Exception("Неверный Bearer-токен");
        }

        $data = DataSource::findOne("UserToken", "token = ? AND user_id = ?", [$bearer_token, $user_id]);
        if($data){
            throw new Exception("Неверный Bearer-токен");
        }

        return $user_id;
    }

    public static function isAuthed(){
        $bearer = Request::getRequestHeaderValue("Bearer");
        try {
            return ($bearer == null) ? false : Request::getSessionUserIDFromBearer($bearer) != null;
        } catch (Exception $e) {
            return false;
        }
    }

}