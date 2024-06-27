<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Events;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class BrowsingHistory
{

    private $maxHistory = 30;
    private $patterns = [
        'create-box',
        'edit-box'
    ];

    /**
     * @param $maxHistory
     * @throws \Exception
     */
    public function __construct($maxHistory = null)
    {
        if ($maxHistory !== null && ctype_digit((string)$maxHistory) && $maxHistory >= 1) {
            $this->maxHistory = $maxHistory;
        }

        // Inizializza la cronologia nella sessione se non è giù stata impostata
        if (!session()->has('__frm_history')) {
            session()->set('__frm_history', []);
        }
    }

    /**
     * @descriotion Aggiunge una nuova URL e l'ora di accesso alla cronologia
     * @param $url
     * @return void
     * @throws \Exception
     */
    public function addUrl($url): void
    {
        $history = session()->get('__frm_history');

        $historyItem = [
            'url' => $url,
            'access_time' => $this->accessTime()
        ];

        $lastPage = $this->getLastPage();

        $insert = (!empty($lastPage) && strcmp($lastPage['url'], currentQueryStringUrl()) == 0)
            ? false
            : true;

        // Aggiunge la url alla sezione
        if ($insert) {
            array_unshift($history, $historyItem);

            // Se la cronologia supera il limite massimo, rimuovi l'elemento più vecchio
            if (count($history) > $this->maxHistory) {
                array_pop($history);
            }

            // aggiorna la sessione
            session()->set('__frm_history', $history);
        }

        // Tokens
        $pattern = '/' . implode('|', $this->patterns) . '/';
        if (preg_match($pattern, $url)) {
            session()->set('__frm_history_t', [
                'access_time' => $this->accessTime(),
                'url' => currentQueryStringUrl()
            ]);
        }
    }

    /**
     * @descriotion Restituisce tutta la cronologia
     * @return array|null
     * @throws \Exception
     */
    public function getHistory(): array|null
    {
        return session()->get('__frm_history');
    }

    /**
     * @descriotion Restituisce il numero di elementi nella cronologia
     * @return int|null
     * @throws \Exception
     */
    public function getHistoryCount(): int|null
    {
        if (session()->has('__frm_history')) {
            return count(session()->get('__frm_history'));
        }
        return null;
    }

    /**
     * @descriotion Restituisce l'ultima pagina nella cronologia
     * @return array|null
     * @throws \Exception
     */
    public function getLastPage(): array|null
    {
        $history = session()->get('__frm_history');
        if (!empty($history)) {
            return $history[0];
        }
        return null;
    }

    /**
     * @descriotion Restituisce il numero dell storico della navigazione
     * @param $n
     * @return array|null
     * @throws \Exception
     */
    public function getNumPage($n = 0): array|null
    {
        $history = session()->get('__frm_history');
        if (!empty($history) && !empty($history[$n])) {
            return $history[$n];
        }

        return null;
    }

    private function accessTime(){
        return date('Y-m-d H:i:s');
    }
}