<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use DateTime;
use DomainException;
use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\CachedKeySet;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use InvalidArgumentException;
use stdClass;
use UnexpectedValueException;

class JWT extends \Firebase\JWT\JWT
{
    private const ASN1_INTEGER = 0x02;
    private const ASN1_SEQUENCE = 0x10;
    private const ASN1_BIT_STRING = 0x03;

    public static function decode(
        string $jwt,
               $keyOrKeyArray,
        stdClass &$headers = null
    ): stdClass {
        $timestamp = \is_null(static::$timestamp) ? \time() : static::$timestamp;

        if (empty($keyOrKeyArray)) {
            throw new InvalidArgumentException('Key may not be empty');
        }
        $tks = \explode('.', $jwt);

        if (\count($tks) !== 3) {
            throw new UnexpectedValueException('Wrong number of segments');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        $headerRaw = static::urlsafeB64Decode($headb64);
        if (null === ($header = static::jsonDecode($headerRaw))) {
            throw new UnexpectedValueException('Invalid header encoding');
        }
        if ($headers !== null) {
            $headers = $header;
        }
        $payloadRaw = static::urlsafeB64Decode($bodyb64);
        if (null === ($payload = static::jsonDecode($payloadRaw))) {
            throw new UnexpectedValueException('Invalid claims encoding');
        }
        if (\is_array($payload)) {
            $payload = (object) $payload;
        }
        if (!$payload instanceof stdClass) {
            throw new UnexpectedValueException('Payload must be a JSON object');
        }
        $sig = static::urlsafeB64Decode($cryptob64);
        if (empty($header->alg)) {
            throw new UnexpectedValueException('Empty algorithm');
        }
        if (empty(static::$supported_algs[$header->alg])) {
            throw new UnexpectedValueException('Algorithm not supported');
        }

        $key = self::getKey($keyOrKeyArray, property_exists($header, 'kid') ? $header->kid : null);

        if (!self::constantTimeEquals($key->getAlgorithm(), $header->alg)) {
            throw new UnexpectedValueException('Incorrect key for this algorithm');
        }
        if (\in_array($header->alg, ['ES256', 'ES256K', 'ES384'], true)) {
            $sig = self::signatureToDER($sig);
        }
        if (!self::verify("{$headb64}.{$bodyb64}", $sig, $key->getKeyMaterial(), $header->alg)) {
            throw new SignatureInvalidException('Signature verification failed');
        }

        if (isset($payload->nbf) && floor($payload->nbf) > ($timestamp + static::$leeway)) {
            throw new BeforeValidException(
                'Cannot handle token with nbf prior to ' . \date(DateTime::ISO8601, (int) $payload->nbf)
            );
        }

        if (!isset($payload->nbf) && isset($payload->iat) && floor($payload->iat) > ($timestamp + static::$leeway)) {
            throw new BeforeValidException(
                'Cannot handle token with iat prior to ' . \date(DateTime::ISO8601, (int) $payload->iat)
            );
        }


        return $payload;
    }


    private static function verify(
        string $msg,
        string $signature,
               $keyMaterial,
        string $alg
    ): bool {
        if (empty(static::$supported_algs[$alg])) {
            throw new DomainException('Algorithm not supported');
        }

        list($function, $algorithm) = static::$supported_algs[$alg];
        switch ($function) {
            case 'openssl':
                $success = \openssl_verify($msg, $signature, $keyMaterial, $algorithm);
                if ($success === 1) {
                    return true;
                }
                if ($success === 0) {
                    return false;
                }

                throw new DomainException(
                    'OpenSSL error: ' . \openssl_error_string()
                );
            case 'sodium_crypto':
                if (!\function_exists('sodium_crypto_sign_verify_detached')) {
                    throw new DomainException('libsodium is not available');
                }
                if (!\is_string($keyMaterial)) {
                    throw new InvalidArgumentException('key must be a string when using EdDSA');
                }
                try {

                    $lines = array_filter(explode("\n", $keyMaterial));
                    $key = base64_decode((string) end($lines));
                    if (\strlen($key) === 0) {
                        throw new DomainException('Key cannot be empty string');
                    }
                    if (\strlen($signature) === 0) {
                        throw new DomainException('Signature cannot be empty string');
                    }
                    return sodium_crypto_sign_verify_detached($signature, $msg, $key);
                } catch (Exception $e) {
                    throw new DomainException($e->getMessage(), 0, $e);
                }
            case 'hash_hmac':
            default:
                if (!\is_string($keyMaterial)) {
                    throw new InvalidArgumentException('key must be a string when using hmac');
                }
                $hash = \hash_hmac($algorithm, $msg, $keyMaterial, true);
                return self::constantTimeEquals($hash, $signature);
        }
    }

    private static function getKey(
        $keyOrKeyArray,
        ?string $kid
    ): Key {
        if ($keyOrKeyArray instanceof Key) {
            return $keyOrKeyArray;
        }

        if (empty($kid) && $kid !== '0') {
            throw new UnexpectedValueException('"kid" empty, unable to lookup correct key');
        }

        if ($keyOrKeyArray instanceof CachedKeySet) {
            // Skip "isset" check, as this will automatically refresh if not set
            return $keyOrKeyArray[$kid];
        }

        if (!isset($keyOrKeyArray[$kid])) {
            throw new UnexpectedValueException('"kid" invalid, unable to lookup correct key');
        }

        return $keyOrKeyArray[$kid];
    }

    private static function handleJsonError(int $errno): void
    {
        $messages = [
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters' //PHP >= 5.3.3
        ];
        throw new DomainException(
            isset($messages[$errno])
                ? $messages[$errno]
                : 'Unknown JSON error: ' . $errno
        );
    }

    private static function safeStrlen(string $str): int
    {
        if (\function_exists('mb_strlen')) {
            return \mb_strlen($str, '8bit');
        }
        return \strlen($str);
    }


    private static function signatureToDER(string $sig): string
    {

        $length = max(1, (int) (\strlen($sig) / 2));
        list($r, $s) = \str_split($sig, $length);

        $r = \ltrim($r, "\x00");
        $s = \ltrim($s, "\x00");

        if (\ord($r[0]) > 0x7f) {
            $r = "\x00" . $r;
        }
        if (\ord($s[0]) > 0x7f) {
            $s = "\x00" . $s;
        }

        return self::encodeDER(
            self::ASN1_SEQUENCE,
            self::encodeDER(self::ASN1_INTEGER, $r) .
            self::encodeDER(self::ASN1_INTEGER, $s)
        );
    }

    private static function encodeDER(int $type, string $value): string
    {
        $tag_header = 0;
        if ($type === self::ASN1_SEQUENCE) {
            $tag_header |= 0x20;
        }

        // Type
        $der = \chr($tag_header | $type);

        // Length
        $der .= \chr(\strlen($value));

        return $der . $value;
    }

    private static function signatureFromDER(string $der, int $keySize): string
    {
        list($offset, $_) = self::readDER($der);
        list($offset, $r) = self::readDER($der, $offset);
        list($offset, $s) = self::readDER($der, $offset);

        $r = \ltrim($r, "\x00");
        $s = \ltrim($s, "\x00");

        $r = \str_pad($r, $keySize / 8, "\x00", STR_PAD_LEFT);
        $s = \str_pad($s, $keySize / 8, "\x00", STR_PAD_LEFT);

        return $r . $s;
    }

    private static function readDER(string $der, int $offset = 0): array
    {
        $pos = $offset;
        $size = \strlen($der);
        $constructed = (\ord($der[$pos]) >> 5) & 0x01;
        $type = \ord($der[$pos++]) & 0x1f;

        // Length
        $len = \ord($der[$pos++]);
        if ($len & 0x80) {
            $n = $len & 0x1f;
            $len = 0;
            while ($n-- && $pos < $size) {
                $len = ($len << 8) | \ord($der[$pos++]);
            }
        }

        // Value
        if ($type === self::ASN1_BIT_STRING) {
            $pos++; // Skip the first contents octet (padding indicator)
            $data = \substr($der, $pos, $len - 1);
            $pos += $len - 1;
        } elseif (!$constructed) {
            $data = \substr($der, $pos, $len);
            $pos += $len;
        } else {
            $data = null;
        }

        return [$pos, $data];
    }
}
