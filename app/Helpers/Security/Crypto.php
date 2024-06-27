<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Security;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * @description Classe per la cifratura e decifratura
 */
class Crypto
{

    /**
     * @param string $data La stringa da crittografare
     * @return false|string
     */
    public static function encrypt(string $data): bool|string
    {
        $ciphering = _env('CIPHER');
        openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryptionIv = _env('ENCRYPTION_IV');
        $encryptionKey = _env('ENCRYPTION_KEY');
        return openssl_encrypt($data, $ciphering, $encryptionKey, $options, $encryptionIv);
    }

    /**
     * @param string $data La stringa da decifrare
     * @return false|string
     */
    public static function decrypt(string $data): bool|string
    {
        $ciphering = _env('CIPHER');
        openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryptionIv = _env('ENCRYPTION_IV');
        $encryptionKey = _env('ENCRYPTION_KEY');
        return openssl_decrypt($data, $ciphering, $encryptionKey, $options, $encryptionIv);
    }

    /**
     * @description Funzione che controlla se la stringa passata è cifrata e la ritorna decifrata nel caso
     * @param string $data Eventuale stringa che se cifrata viene decifrata
     * @return string|bool
     */
    public static function verify(string $data = ''): string|bool
    {
        $decrypt = self::decrypt($data);
        $encrypt = self::encrypt($decrypt);

        if((string)$encrypt===(string)$data && (bool)mb_detect_encoding($decrypt, CHARSET, true) === true ){
            return  $decrypt;
        }

        return $data;
    }

    /**
     * @param string $string Stringa da verificare
     * @return string|bool
     */
    private static function isValidString(string $string = ''): string|bool
    {
        if (preg_match('%^(?:
                  [\x09\x0A\x0D\x20-\x7E]
                | [\xC2-\xDF][\x80-\xBF]
                | \xE0[\xA0-\xBF][\x80-\xBF]
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
                | \xED[\x80-\x9F][\x80-\xBF]
                | \xF0[\x90-\xBF][\x80-\xBF]{2}
                | [\xF1-\xF3][\x80-\xBF]{3}
                | \xF4[\x80-\x8F][\x80-\xBF]{2}
            )*$%x', $string))
            return $string;
        else
            return @iconv('CP1252', CHARSET, $string);
    }

}
