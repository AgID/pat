<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ConcurrentSessModel;
use System\Input;
use System\Token;
use System\Validator;

/**
 * Controller per la disconnessione delle sessioni concorrenti.
 */
class ConcurrentSessionDisableAdminController extends BaseAuthController
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('not_acl');
    }

    /**
     * Richiede la disconnessione di un'altra sessione dell'utente autenticato.
     *
     * @url /admin/concurrent/sess/:num.html
     * @method POST
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        if (!Token::verify()) {
            showError('OPS', 'Operazione non autorizzata.', 403, true);
        }

        $validator = new Validator();
        $validator->label('ID sessione')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->end();

        $validator->label('Url')
            ->value(Input::post('r'))
            ->required()
            ->isUrl(true)
            ->end();

        if (!$validator->isSuccess()) {
            showError('OPS', 'I parametri non soddisfano la richiesta.', 403, true);
        }

        $identity = $this->auth->getIdentity();
        $sessionRecordId = (int)uri()->segment(4, 0);

        ConcurrentSessModel::where('id', $sessionRecordId)
            ->where('user_id', (int)$identity['id'])
            ->where('institution_id', checkAlternativeInstitutionId())
            ->where('created_at', '>=', date("Y-m-d H:i:s", strtotime("-8 hours")))
            ->where('sess_id', '!=', session()->getId())
            ->where('destroyed_sess_id', 0)
            ->update(['destroyed_sess_id' => 1]);

        $redirectUrl = Input::post('r', true);
        $applicationUrl = parse_url(siteUrl());
        $targetUrl = parse_url($redirectUrl);
        $sameOrigin = isset($applicationUrl['scheme'], $applicationUrl['host'], $targetUrl['scheme'], $targetUrl['host'])
            && strtolower($applicationUrl['scheme']) === strtolower($targetUrl['scheme'])
            && strtolower($applicationUrl['host']) === strtolower($targetUrl['host'])
            && ($applicationUrl['port'] ?? null) === ($targetUrl['port'] ?? null);

        if (!$sameOrigin) {
            $redirectUrl = siteUrl('admin/dashboard');
        }

        redirect($redirectUrl, 'auto', 303);
    }
}
