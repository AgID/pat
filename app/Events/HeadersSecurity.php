<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Events;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class HeadersSecurity
{
    public function handle(): void
    {
        if (headers_sent()) {
            return;
        }

        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
            header_remove('Server');
            header_remove('Upgrade');
        }

        // 2. Header di Sicurezza
        $headers = [
            'Server' => 'Server',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), payment=()',
            'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Content-Security-Policy' => "object-src 'none'; base-uri 'self'; frame-ancestors 'self'",
        ];

        foreach ($headers as $key => $value) {
            header("$key: $value");
        }

        // Gestione CORS Centralizzata
        $this->handleCors();

        // 3. Sicurezza Cookie
        if(ENVIRONMENT == 'production') {
            $this->secureCookies();
        }
    }

    private function handleCors(): void
    {
        $isValidCall = false;
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        if (preg_match('#^/(status|oidc)/#i', $requestUri) || $requestUri === '/status') {
            $isValidCall = true;
        }

        // 2. Controllo se è una chiamata AJAX (XMLHttpRequest)
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $isValidCall = true;
        }

        // Limita la gestione CORS ai percorsi previsti e alle richieste AJAX.
        if (!$isValidCall) {
            return;
        }

        // 3. Verifica se è una richiesta che richiede CORS (presenza di Origin)
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (empty($origin)) {
            return; // Non è una richiesta cross-origin o Origin non inviato -> niente header CORS
        }

        // 4. Definisci la Whitelist
        $allowedOrigins = [];

        // Recupera schema e host corrente
        if (isset($_SERVER['HTTP_HOST'])) {
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            
            // Gestione porta (se diversa da 80/443)
            $port = '';
            if (isset($_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], ['80', '443'])) {
                 $port = ':' . $_SERVER['SERVER_PORT'];
            }

            // Pulisci host da 'www.' per avere la base
            $rawHost = $_SERVER['HTTP_HOST'];
            // Rimuove eventuale porta dall'host per la manipolazione stringa
            $hostNoPort = explode(':', $rawHost)[0];
            $baseHost = preg_replace('/^www\./i', '', $hostNoPort);

            // Aggiungi entrambe le varianti (con e senza www) + porta eventuale
            $allowedOrigins[] = $scheme . '://' . $baseHost . $port;
            $allowedOrigins[] = $scheme . '://www.' . $baseHost . $port;
        }

        // Aggiungi domini esterni configurati (da Config/app.php)
        $configFile = APP_PATH . 'Config/app.php';
        if (file_exists($configFile)) {
            $appConfig = require $configFile;
            if (!empty($appConfig['cors_allowed_origins']) && is_array($appConfig['cors_allowed_origins'])) {
                foreach ($appConfig['cors_allowed_origins'] as $eo) {
                    $allowedOrigins[] = trim($eo);
                }
            }
        }
        
        // 3. Verifica e Applica Header
        // Il confronto e' stretto di proposito: in_array() in modalita' lasca considera uguale
        // a qualunque stringa non vuota un valore non stringa, e basterebbe a far passare il
        // controllo di whitelist.
        if (in_array($origin, $allowedOrigins, true)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Accept, Origin");
            header("Access-Control-Max-Age: 86400");
            header("Vary: Origin");
        }

        // 4. Gestione Preflight (OPTIONS)
        // Se è una richiesta OPTIONS e l'origine è valida, termina qui per evitare esecuzione inutile
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS' && in_array($origin, $allowedOrigins, true)) {
            // Rispondi 200/204 OK e termina
            http_response_code(204);
            exit();
        }
    }

    private function secureCookies(): void
    {
        // Emula .htaccess: Header always edit Set-Cookie (.*) "$1; HttpOnly; Secure; SameSite=Strict"
        $currentHeaders = headers_list();
        $cookies = [];

        foreach ($currentHeaders as $header) {
            if (stripos($header, 'Set-Cookie:') === 0) {
                $cookies[] = substr($header, 11); // Rimuove 'Set-Cookie: '
            }
        }

        if (!empty($cookies) && function_exists('header_remove')) {
            header_remove('Set-Cookie');

            foreach ($cookies as $cookie) {
                // Aggiunge flag se mancanti
                if (stripos($cookie, 'HttpOnly') === false) {
                    $cookie .= '; HttpOnly';
                }
                if (stripos($cookie, 'Secure') === false) {
                    $cookie .= '; Secure';
                }
                // Gestione SameSite
                if (stripos($cookie, 'SameSite') === false) {
                    $cookie .= '; SameSite=Strict';
                } elseif (preg_match('/SameSite=(Lax|None)/i', $cookie)) {
                    // Forza Strict come da regola originale .htaccess
                     $cookie = preg_replace('/; ?SameSite=[a-zA-Z]+/i', '; SameSite=Strict', $cookie);
                }

                header("Set-Cookie: $cookie", false);
            }
        }
    }
}
