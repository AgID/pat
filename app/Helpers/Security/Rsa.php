<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Security;

use OpenSSLAsymmetricKey;
use OpenSSLCertificate;
use RuntimeException;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class Rsa
{
    const SHA512 = 'sha512';
    const SHA246 = 'sha256';

    const BITS_512 = 512;
    const BITS_1024 = 1024;
    const BITS_2056 = 2056;
    const BITS_4096 = 4096;

    const KEY_TYPE = OPENSSL_KEYTYPE_RSA;

    // Tipi di algo supportati.
    // https://www.php.net/manual/en/function.openssl-get-md-methods.php
    const ALGO_DSA_SHA1 = 'DSA-SHA1';
    const ALGO_DSA_SHA1_OLD = 'DSA-SHA1-old';
    const ALGO_DSS1 = 'DSS1';
    const ALGO_RSA_MD2 = 'RSA-MD2';
    const ALGO_RSA_MD4 = 'RSA-MD4';
    const ALGO_RSA_MD5 = 'RSA-MD5';
    const ALGO_RSA_RIPEMD160 = 'RSA-RIPEMD160';
    const ALGO_RSA_SHA = 'RSA_SHA';
    const ALGO_RSA_SHA1 = 'RSA-SHA1';
    const ALGO_RSA_SHA1_2 = 'RSA-SHA1-2';
    const ALGO_RSA_SHA224 = 'RSA-SHA224';
    const ALGO_RSA_SHA256 = 'RSA-SHA256';
    const ALGO_RSA_SHA384 = 'RSA-SHA384';
    const ALGO_RSA_SHA512 = 'RSA-SHA512';
    const ALGO_DSA_WITH_SHA1 = 'dsaWithSHA1';
    const ALGO_DSSL = 'dss1';
    const ALGO_MD2_WITH_RSA_ENCRYPTION = 'md2WithRSAEncryption';
    const ALGO_MD4_WITH_RSA_ENCRYPTION = 'md4WithRSAEncryption';
    const ALGO_MD5_WITH_RSA_ENCRYPTION = 'md5WithRSAEncryption';
    const ALGO_RIPEMD = 'ripemd';
    const ALGO_RIPEMD_160_WITH_RSA = 'ripemd160WithRSA';
    const ALGO_RMD_160 = 'rmd160';
    const ALGO_SHA1_WITH_RSA_ENCRYPTION = 'sha1WithRSAEncryption';
    const ALGO_SHA224_WITH_RSA_ENCRYPTION = 'sha224WithRSAEncryption';
    const ALGO_SHA256_WITH_RSA_ENCRYPTION = 'sha256WithRSAEncryption';
    const ALGO_SHA384_WITH_RSA_ENCRYPTION = 'sha384WithRSAEncryption';
    const ALGO_SHA512_WITH_RSA_ENCRYPTION = 'sha512WithRSAEncryption';
    const ALGO_SHA_WITH_RSA_ENCRYPTION = 'shaWithRSAEncryption';
    const ALGO_SSL2_MD5 = 'ssl2-md5';
    const ALGO_SSL3_MD5 = 'ssl3-md5';
    const ALGO_SSL3_SHA1 = 'ssl3-sha1';

    private $privateKey = null;
    private $publicKey = null;
    private $config = null;
    private $data = '';

    /**
     * @param $data
     * @param int $privateKeyBits
     * @param int $privateKeyType
     * @param $digestAlg
     */
    public function __construct(int $privateKeyBits = 2048, int $privateKeyType = OPENSSL_KEYTYPE_RSA, $digestAlg = null)
    {
        // Verifica se sono presenti le funzioni necessarie dell'OPEN SSL
        if (function_usable('openssl_pkey_new') === false ||
            function_usable('openssl_pkey_export') === false ||
            function_usable('openssl_pkey_get_details') === false ||
            function_usable('openssl_sign') === false ||
            function_usable('openssl_verify') === false
        ) {
            throw new RuntimeException('alcune o non tutte le funzioni di openssl');
        }

        if ($privateKeyBits !== null) {
            $this->config['private_key_bits'] = $privateKeyBits;
        }

        if ($privateKeyType !== null) {
            $this->config['private_key_type'] = $privateKeyType;
        }

        if ($digestAlg !== null) {
            $this->config['digest_alg'] = $digestAlg;
        }

        $this->buildRSA();
    }

    public static function __callStatic($method, $arguments)
    {
        $variables = count($arguments) ? current($arguments) : [];

        return new static($method, $variables);
    }

    /**
     * @Description "Costruttore" per generare le chiavi pubbliche e private.
     * @param $privateKeyBits
     * @param $privateKeyType
     * @param $digestAlg
     * @return static
     */
    public static function create($privateKeyBits = 2048, $privateKeyType = OPENSSL_KEYTYPE_RSA, $digestAlg = null): static
    {
        return new static($privateKeyBits, $privateKeyType, $digestAlg);
    }

    /**
     * @description Genera la chiave privata e pubblica
     * @return void
     */
    private function buildRSA(): void
    {
        // Genero nuova chiave SSL
        $newKeyPair = openssl_pkey_new($this->config);

        // Genero la chiave privata
        openssl_pkey_export($newKeyPair, $privateKeyPem);

        // Prendo i dettagli della chiave privata per generare quella pubblica
        $details = openssl_pkey_get_details($newKeyPair);

        // Chiave privata e pubblica
        $this->privateKey = $privateKeyPem;
        $this->publicKey = $details['key'];

    }

    /**
     * @Description Ritorna la chiave privata
     * @return null
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @Description Ritorna la chiave pubblica
     * @return null
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @Description Aggiunge la firma da un dato
     * @param string|null $data Dato su cui aggiungere la firma
     * @param array|string|OpenSSLAsymmetricKey|OpenSSLCertificate|null $privateKey Chiave privata
     * @param int|string|null $algo Algoritmo di firma
     * @return string
     */
    public function sign(string $data = null, array|OpenSSLAsymmetricKey|string|OpenSSLCertificate $privateKey = null, int|string $algo = null): string
    {
        $algo = ($algo === null) ? OPENSSL_ALGO_SHA256 : $algo;

        openssl_sign($data, $signature, $privateKey, $algo);

        return $signature;
    }

    /**
     * @Description Verifica la firma di un dato
     * @param string|null $data La stringa di dati utilizzata per generare la firma in precedenza
     * @param string|null $signature Una stringa binaria grezza, generata da openssl_sign() o da mezzi simili.
     * @param OpenSSLAsymmetricKey|OpenSSLCertificate|array|string|null $publicKey Chiave pubblica
     * @param string|int $algo Int - Un algoritmo di firma | string - una stringa valida restituita da openssl_get_md_methods(),
     *                                                                             ad esempio "sha1WithRSAEncryption" o "sha512".
     * @return bool|int
     */
    public function verify(?string $data = null, ?string $signature = null, OpenSSLAsymmetricKey|OpenSSLCertificate|array|string|null $publicKey = null, string|int $algo = "sha256WithRSAEncryption"): bool|int
    {
        return openssl_verify($data, $signature, $publicKey, $algo);
    }
}
