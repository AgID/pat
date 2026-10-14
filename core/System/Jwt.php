<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Firebase\JWT\Key;
use stdClass;

/**
 * Wrapper class per la libreria Firebase JWT
 * Fornisce un'interfaccia semplificata per l'encoding e decoding dei JWT
 */
class Jwt
{
    /**
     * Encode a payload into a JWT token
     *
     * @param array|object $payload The payload to encode
     * @param string $key The secret key
     * @param string $alg The algorithm to use (default: HS256)
     * @return string The JWT token
     */
    public static function encode($payload, $key, $alg = 'HS256')
    {
        return \Helpers\JWT::encode($payload, $key, $alg);
    }

    /**
     * Decode a JWT token
     *
     * @param string $jwt The JWT token to decode
     * @param string $key The secret key
     * @param array $allowed_algs Array of allowed algorithms (default: ['HS256'])
     * @return stdClass The decoded payload
     */
    public static function decode($jwt, $key, $allowed_algs = ['HS256'])
    {
        // Crea un oggetto Key per la libreria Firebase JWT
        $keyObject = new Key($key, $allowed_algs[0]);
        
        return \Helpers\JWT::decode($jwt, $keyObject);
    }
}