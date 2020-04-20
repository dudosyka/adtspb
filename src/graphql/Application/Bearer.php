<?php

use GraphQL\Application\Entity\User;
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
            ->getToken(); // Retrieves the generated token

        return $token;
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

}