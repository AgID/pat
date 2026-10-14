<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Autorization
{
    public static function getSettings()
    {
        return [
            'token_timeout' => config('jwt.token_timeout'),
            'jwt_key' => config('jwt.jwt_key'),
        ];
    }

    public static function validateTimestamp($token)
    {
        $tokenTimeOut = config('jwt.token_timeout');
 
        $token = self::validateToken($token);
        if ($token != false && !empty($token->timestamp) && (now() - $token->timestamp < ($tokenTimeOut * 60))) {
            return $token;
        }
        return false;
    }
 
    public static function validateToken($token)
    {
        $jwtKey = config('jwt.jwt_key');
        return Jwt::decode($token, $jwtKey);
    }
 
    public static function generateToken($data)
    {
        $jwtKey = config('jwt.jwt_key');
        return Jwt::encode($data, $jwtKey);
    }
}

