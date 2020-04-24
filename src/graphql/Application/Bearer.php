<?php
namespace GraphQL\Application;

use Exception;
use GraphQL\Application\Database\DataSource;
use GraphQL\Application\Entity\User;
use InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;

class Bearer
{

    public static function generate(\GraphQL\Application\AppContext $context, User $user = null, int $be_used_after = 3600, int $expires = 86400){

        $current_user = $user ?: $context->viewer;

        $time = time();

        $token = (new Builder())->issuedBy($context->rootUrl) // Configures the issuer (iss claim)
            ->permittedFor($context->rootUrl) // Configures the audience (aud claim)
            ->identifiedBy('uid'.$current_user->id, true) // Configures the id (jti claim), replicating as a header item
            ->issuedAt($time) // Configures the time that the token was issue (iat claim)
            ->canOnlyBeUsedAfter($time + $be_used_after) // Configures the time that the token can be used (nbf claim)
            ->expiresAt($time + $expires) // Configures the expiration time of the token (exp claim)
            ->withClaim('uid', $current_user->id) // Configures a new claim, called "uid"
            ->withClaim('ip', $context->ip)
            ->getToken(); // Retrieves the generated token

        return $token;
    }

    /**
     * @param $header
     * @return string
     * @throws Exception
     */
    public static function getBearerFromHeader($header){
        $bearer = explode(" ", $header);
        if($bearer[0] != "Bearer")
            throw new \Exception("Неверный формат Authorization-заголовка (требуется наличие кодового слова Bearer)");

        try {
            self::parseBearer($bearer[1]);
        } catch(InvalidArgumentException $e){
            throw new \Exception("Неверный формат Authorization-заголовка (некорректный Bearer-токен)");
        }

        return (string)$bearer[1];
    }

    /**
     * @param string $token
     * @return \Lcobucci\JWT\Token
     */

    public static function parseBearer(string $token){
        $tk = (new Parser())->parse((string) $token);
        return $tk;
    }

    //TODO: validation https://github.com/lcobucci/jwt/blob/3.3/README.md


    /**
     * @param string $bearer_token
     * @return mixed
     * @throws Exception
     */
    public static function getUserIDFromBearer(string $bearer_token){

        $token = Bearer::parseBearer($bearer_token);
        $user_id = $token->getClaim("uid");
        if($user_id == "" || $user_id == null || !isset($user_id)){
            throw new Exception("Пользователь использовал неверный Bearer-токен: uid не найден в токене.");
        }

        $data = DataSource::findOne("UserToken", "token = :token AND user_id = :uid", [
            "token" => $bearer_token,
            "uid" => $user_id
        ]);
        if($data == null){
            throw new Exception("Пользователь использовал неверный Bearer-токен: токен не найден в системе с таким uid.");
        }

        return $user_id;
    }

}