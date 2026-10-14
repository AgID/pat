<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Events;

class BrowsingHistory
{
    /**
     * Frammenti di url che identificano un form di creazione/modifica.
     */
    private $patterns = [
        'create-box',
        'edit-box'
    ];

    /**
     * Se l'url e' quella di un form di creazione/modifica, ne registra l'origine in sessione.
     *
     * @param $url
     * @return void
     * @throws \Exception
     */
    public function addUrl($url): void
    {
        $pattern = '/' . implode('|', $this->patterns) . '/';

        if (preg_match($pattern, $url)) {
            session()->set('__frm_history_t', [
                'access_time' => date('Y-m-d H:i:s'),
                'url' => currentQueryStringUrl()
            ]);
        }
    }
}
