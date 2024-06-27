<?php
/**
 * Copyright (c) 2016, Taylor Hornby
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Password
{
    const PBKDF2_HASH_ALGORITHM = "sha256";
    const PBKDF2_ITERATIONS = 64000;
    const PBKDF2_SALT_BYTES = 24;
    const PBKDF2_OUTPUT_BYTES = 18;

    const HASH_SECTIONS = 5;
    const HASH_ALGORITHM_INDEX = 0;
    const HASH_ITERATION_INDEX = 1;
    const HASH_SIZE_INDEX = 2;
    const HASH_SALT_INDEX = 3;
    const HASH_PBKDF2_INDEX = 4;


    public static function hash($password)
    {
        if (!\is_string($password)) {
            throw new \Exception(
                "hash(): Expected a string"
            );
        }
        if (\function_exists('random_bytes')) {
            try {
                $salt_raw = \random_bytes(self::PBKDF2_SALT_BYTES);
            } catch (Error $e) {
                $salt_raw = false;
            } catch (Exception $e) {
                $salt_raw = false;
            } catch (TypeError $e) {
                $salt_raw = false;
            }
        } else {
            $salt_raw = @\mcrypt_create_iv(self::PBKDF2_SALT_BYTES, MCRYPT_DEV_URANDOM);
        }
        if ($salt_raw === false) {
            throw new \CannotPerformOperationException(
                "Random number generator failed. Not safe to proceed."
            );
        }

        $PBKDF2_Output = self::pbkdf2(
            self::PBKDF2_HASH_ALGORITHM,
            $password,
            $salt_raw,
            self::PBKDF2_ITERATIONS,
            self::PBKDF2_OUTPUT_BYTES,
            true
        );

        return self::PBKDF2_HASH_ALGORITHM .
            ":" .
            self::PBKDF2_ITERATIONS .
            ":" .
            self::PBKDF2_OUTPUT_BYTES .
            ":" .
            \base64_encode($salt_raw) .
            ":" .
            \base64_encode($PBKDF2_Output);
    }


    public static function verify($password, $hash)
    {
        if (!\is_string($password) || !\is_string($hash)) {
            throw new \Exception(
                "verify(): Expected two strings"
            );
        }
        $params = \explode(":", $hash);
        if (\count($params) !== self::HASH_SECTIONS) {
            throw new \InvalidHashException(
                "Fields are missing from the password hash."
            );
        }

        $pbkdf2 = \base64_decode($params[self::HASH_PBKDF2_INDEX], true);
        if ($pbkdf2 === false) {
            throw new \InvalidHashException(
                "Base64 decoding of pbkdf2 output failed."
            );
        }

        $salt_raw = \base64_decode($params[self::HASH_SALT_INDEX], true);
        if ($salt_raw === false) {
            throw new \InvalidHashException(
                "Base64 decoding of salt failed."
            );
        }

        $storedOutputSize = (int)$params[self::HASH_SIZE_INDEX];
        if (self::ourStrlen($pbkdf2) !== $storedOutputSize) {
            throw new \InvalidHashException(
                "PBKDF2 output length doesn't match stored output length."
            );
        }

        $iterations = (int)$params[self::HASH_ITERATION_INDEX];
        if ($iterations < 1) {
            throw new \InvalidHashException(
                "Invalid number of iterations. Must be >= 1."
            );
        }

        return self::slow_equals(
            $pbkdf2,
            self::pbkdf2(
                $params[self::HASH_ALGORITHM_INDEX],
                $password,
                $salt_raw,
                $iterations,
                self::ourStrlen($pbkdf2),
                true
            )
        );
    }


    public static function slow_equals($a, $b)
    {
        if (!\is_string($a) || !\is_string($b)) {
            throw new \Exception(
                "slow_equals(): expected two strings"
            );
        }
        if (\function_exists('hash_equals')) {
            return \hash_equals($a, $b);
        }

        $diff = self::ourStrlen($a) ^ self::ourStrlen($b);
        for ($i = 0; $i < self::ourStrlen($a) && $i < self::ourStrlen($b); $i++) {
            $diff |= \ord($a[$i]) ^ \ord($b[$i]);
        }
        return $diff === 0;
    }


    public static function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
    {
        if (!\is_string($algorithm)) {
            throw new \Exception(
                "pbkdf2(): algorithm must be a string"
            );
        }
        if (!\is_string($password)) {
            throw new \Exception(
                "pbkdf2(): password must be a string"
            );
        }
        if (!\is_string($salt)) {
            throw new \Exception(
                "pbkdf2(): salt must be a string"
            );
        }
        $count += 0;
        $key_length += 0;

        $algorithm = \strtolower($algorithm);
        if (!\in_array($algorithm, \hash_algos(), true)) {
            throw new \CannotPerformOperationException(
                "Invalid or unsupported hash algorithm."
            );
        }

        $ok_algorithms = array(
            "sha1", "sha224", "sha256", "sha384", "sha512",
            "ripemd160", "ripemd256", "ripemd320", "whirlpool"
        );
        if (!\in_array($algorithm, $ok_algorithms, true)) {
            throw new \CannotPerformOperationException(
                "Algorithm is not a secure cryptographic hash function."
            );
        }

        if ($count <= 0 || $key_length <= 0) {
            throw new \CannotPerformOperationException(
                "Invalid PBKDF2 parameters."
            );
        }

        if (\function_exists("hash_pbkdf2")) {
            if (!$raw_output) {
                $key_length = $key_length * 2;
            }
            return \hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
        }

        $hash_length = self::ourStrlen(\hash($algorithm, "", true));
        $block_count = \ceil($key_length / $hash_length);

        $output = "";
        for ($i = 1; $i <= $block_count; $i++) {
            $last = $salt . \pack("N", $i);
            $last = $xorsum = \hash_hmac($algorithm, $last, $password, true);
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = \hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if ($raw_output) {
            return self::ourSubstr($output, 0, $key_length);
        } else {
            return \bin2hex(self::ourSubstr($output, 0, $key_length));
        }
    }


    private static function ourStrlen($str)
    {
        static $exists = null;
        if ($exists === null) {
            $exists = \function_exists('mb_strlen');
        }

        if (!\is_string($str)) {
            throw new \Exception(
                "ourStrlen() expects a string"
            );
        }

        if ($exists) {
            $length = \mb_strlen((string) $str, '8bit');
            if ($length === false) {
                throw new \Exception();
            }
            return $length;
        } else {
            return \strlen((string) $str);
        }
    }


    private static function ourSubstr($str, $start, $length = null)
    {
        static $exists = null;
        if ($exists === null) {
            $exists = \function_exists('mb_substr');
        }
        if (!\is_string($str)) {
            throw new \Exception(
                "ourSubstr() expects a string"
            );
        }

        if ($exists) {
            if (!isset($length)) {
                if ($start >= 0) {
                    $length = self::ourStrlen($str) - $start;
                } else {
                    $length = -$start;
                }
            }

            return \mb_substr($str, $start, $length, '8bit');
        }

        if (isset($length)) {
            return \substr($str, $start, $length);
        } else {
            return \substr($str, $start);
        }
    }
}
