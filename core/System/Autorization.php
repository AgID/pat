<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
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
        $tokenTimeOut = \Config::get('token_timeout');

        $token = self::validateToken($token);
        if ($token != false && !empty($token->timestamp) && (now() - $token->timestamp < ($tokenTimeOut * 60))) {
            return $token;
        }
        return false;
    }

    public static function validateToken($token)
    {
        $jwtKey = \Config::get('jwt_key');
        return JWT::decode($token, $jwtKey);
    }

    public static function generateToken($data)
    {
        $jwtKey = \Config::get('jwt_key');
        return JWT::encode($data, $jwtKey);
    }
}

