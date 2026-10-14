<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;


defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Classe per la gestione dei bottoni delle azioni sui record in base ai permessi dell'utente
 *
 * @phpstan-consistent-constructor
 */
class ButtonAction
{
    private string $tagOpenDesktop = '<!-- Action -->
            <div class="dropdown dropdown-azioni dropdown-btn-desktop">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false" ></button>
            <div class="dropdown-menu dropdown-menu-right" role="menu">
    ';
    private string $tagOpenMobile = '<!-- Action -->
            <div class="btn-toolbar dropdown-btn-mobile" role="toolbar" aria-label="Toolbar Azioni">
            <div class="btn-group" role="group" aria-label="Azioni">
    ';
    private string $tagCloseDesktop = '</div></div><!-- /Action -->';
    private string $tagCloseMobile = '</div></div><!-- /Action -->';
    protected string $htmlDesktop = '';
    protected string $htmlMobile = '';
    protected array $profile = [
        'view' => false, // Permits
        'edit' => false, // Permits
        'duplicate' => false, // Permits
        'delete' => false, // Permits
        'versioning' => false, // Profilo ACL
        'archiving' => false, // Profilo ACL
        'lock_unlock' => false, // Profilo ACL
        'cancel' => false,
        'archive' => false,
        'extension' => false,
        'scp' => false,
        'assign_editor' => false,
    ];
    private string $editOutsideButton = '';
    /**
     * Accetta come parametro un array contente le info per visualizzare i pulsanti delle action
     *
     * @param array|null $profiles Profili ACL utilizzati per mostrare o meno i pulsanti delle action
     */
    public function __construct(?array $profiles)
    {
        helper('url');

        $this->htmlDesktop = $this->tagOpenDesktop;
        $this->htmlMobile = $this->tagOpenMobile;

        if (!empty($profiles)) {

            foreach ($profiles as $key => $value) {

                if (array_key_exists($key, $this->profile)) {

                    $this->profile[$key] = in_array($value, [true, false]) ? $value : false;

                }

            }
        }


    }

    /**
     * Aggiunge una funzione di callback e i suoi argomenti per essere eseguita e imposta il risultato come HTML per le versioni desktop e mobile.
     *
     * @param callable $callback     La funzione di callback da eseguire
     * @param mixed    ...$arguments Un numero variabile di argomenti da passare alla funzione di callback
     *
     * @return self L'istanza della classe, consentendo la catena di metodi
     */
    public function add(callable $callback, ...$arguments): self
    {
        $result = call_user_func_array($callback, $arguments);

        if (!empty($result) &&
            is_array($result) &&
            !empty($result['html_desktop']) &&
            !empty($result['html_mobile'])
        ) {

            $this->htmlDesktop .= $result['html_desktop'];
            $this->htmlMobile .= $result['html_mobile'];
        }
        return $this;
    }

    /**
     * Funzione che aggiunge il bottone per la visualizzazione dei dettagli di un record
     *
     * @param string|null     $url url del metodo
     * @param int|string|null $id  id dell'elemento di cui visualizzare i dettagli
     * @return $this
     */
    public function addView(?string $url = '#!', int|string|null $id = ''): static
    {
        if ($this->profile['view'] === true) {
            $this->htmlDesktop .= anchor(
                    $url,
                    '<i class="fas fa-eye"></i>',
                    'title="" id="view_' . $id . '" class="_record-view btn btn-sm btn-primary record-eye dropdown-item" data-toggle="tooltip"  data-placement="top" data-original-title="Visualizza elemento" aria-label="Visualizza elemento"'
                ) . "\n";

            $this->htmlMobile .= anchor(
                    $url,
                    '<i class="fas fa-eye"></i>',
                    'title="" id="view_' . $id . '" class="_record-view btn btn-sm btn-primary record-eye" data-toggle="tooltip"  data-placement="top" data-original-title="Visualizza elemento" aria-label="Visualizza elemento"'
                ) . "\n";
        }

        return $this;
    }

    /**
     * Funzione che aggiunge il bottone per la modifica di un record
     *
     * @param string          $url url del metodo
     * @param int|string|null $id  id del record da modificare
     * @return $this
     */
    public function addEdit(string $url = '#!', int|string|null $id = '', string $params='', bool $outsideDropdown = false): static
    {
        if ($this->profile['edit'] === true) {

            if ($outsideDropdown) {

                $this->editOutsideButton = anchor(
                    $url,
                    '<i class="fas fa-edit"></i>',
                    'title="" id="edit_' . $id . '" ' . $params . '
                        class="btn btn-sm btn-outline-secondary"
                        style="padding: 0.1rem 0.5rem"
                        data-toggle="tooltip"
                        data-placement="top"
                        data-original-title="Modifica voce"
                        aria-label="Modifica voce"'
                );

            } else {

                $this->htmlDesktop .= anchor(
                        $url,
                        '<i class="fas fa-edit"></i>',
                        'title="" id="edit_' . $id . '" ' . $params . '
                            class="btn btn-sm btn-primary _record-edit dropdown-item"
                            data-toggle="tooltip"
                            data-placement="top"
                            data-original-title="Modifica voce"
                            aria-label="Modifica voce"'
                    ) . "\n";
            }

            $this->htmlMobile .= anchor(
                    $url,
                    '<i class="fas fa-edit"></i>',
                    'title="" id="edit_' . $id . '" ' . $params . '  class="btn btn-sm btn-primary _record-edit" data-toggle="tooltip"  data-placement="top" data-original-title="Modifica voce" aria-label="Modifica voce"'
                ) . "\n";
        }
        return $this;
    }

    /**
     * Funzione che aggiunge il bottone per la duplicazione di un record
     *
     * @param string          $url url del metodo
     * @param int|string|null $id  id del record da duplicare
     * @return $this
     */
    public function addDuplicate(string $url = '#!', int|string|null $id = ''): static
    {
        if ($this->profile['duplicate'] === true) {
            $this->htmlDesktop .= anchor(
                    $url,
                    '<i class="fas fa-clone"></i>',
                    'title="" id="duplicate_' . $id . '" class="btn btn-sm btn-primary _record-duplicate dropdown-item" data-toggle="tooltip" data-placement="top" data-original-title="Duplica voce" aria-label="Duplica voce"'
                ) . "\n";


            $this->htmlMobile .= anchor(
                    $url,
                    '<i class="fas fa-clone"></i>',
                    'title="" id="duplicate_' . $id . '" class="btn btn-sm btn-primary _record-duplicate" data-toggle="tooltip" data-placement="top" data-original-title="Duplica voce" aria-label="Duplica voce"'
                ) . "\n";
        }
        return $this;
    }

    /**
     * Funzione che aggiunge il bottone per l'eliminazione di un record
     *
     * @param string          $url url del metodo
     * @param int|string|null $id  id del record da eliminare
     * @return $this
     */
    public function addDelete(string $url = '#!', int|string|null $id = '', string $params=''): static
    {
        if ($this->profile['delete'] === true) {
            $this->htmlDesktop .= anchor(
                    $url,
                    '<i class="fas fa-trash"></i>',
                    'title="" id="delete_' . $id . '" ' . $params . '  class="btn btn-sm btn-danger record-delete dropdown-item" data-toggle="tooltip" data-placement="top" data-original-title="Elimina voce" aria-label="Elimina voce"'
                ) . "\n";

            $this->htmlMobile .= anchor(
                    $url,
                    '<i class="fas fa-trash"></i>',
                    'title="" id="delete_' . $id . '" ' . $params . '  class="btn btn-sm btn-danger record-delete" data-toggle="tooltip" data-placement="top" data-original-title="Elimina voce" aria-label="Elimina voce"'
                ) . "\n";
        }
        return $this;
    }

    /**
     * @description Funzione che aggiunge un bottone custom di un record
     *
     * @param string          $tag     Nome tag
     * @param string          $url     url del metodo
     * @param int|string|null $id      id del record da archiviare
     * @param string|null     $type    Tipologia
     * @param string|null     $name    Nome
     * @param string|null     $permits Permessi
     * @return $this
     */
    public function addCustomButton(string $tag = '', string $url = '#!', int|string|null $id = '', ?string $type = '', ?string $name = '', ?string $permits = ''): static
    {
        // Queste operazioni non sono disponibili, anche per profili salvati in precedenza.
        if ($permits === 'versioning' || $permits === 'archiving') {
            return $this;
        }

        $this->htmlDesktop .= false;

        $this->htmlMobile .= false;

        return $this;
    }

    /**
     * @description Metodo che attiva o disattiva un record
     *
     * @param string          $url  url del metodo
     * @param int             $lock Indica se è un operazione di blocco
     *                              o sblocco
     * @param int|string|null $id   id del record da bloccare/sbloccare
     * @param string|null     $type Tipologia
     * @return $this
     */
    public function addLockUnlock(string $url = '#!', int $lock = 1, int|string|null $id = '', ?string $type = 'user'): static
    {
        $textLockUnlock = ($lock === 0) ? 'Disattiva' : 'Attiva';
        $lockUnlockVoice = ($type === 'user') ? 'Utente' : 'Ente';

        if ($this->profile['lock_unlock']) {
            $this->htmlDesktop .= anchor(
                    $url,
                    ($type === 'user') ? '<i class="fas fa-user-lock"></i>' : (($lock) ? '<i class="fas fa-lock-open"></i>' : '<i class="fas fa-lock"></i>'),
                    'title="" id="_lock_unlock' . $id . '" class="__record_lock_unlock btn btn-sm btn-primary dropdown-item" data-blocked="'.$lock.'" data-toggle="tooltip" data-placement="top" data-original-title="' . $textLockUnlock . ' ' . $lockUnlockVoice . '"'
                ) . "\n";

            $this->htmlMobile .= anchor(
                    $url,
                    ($type === 'user') ? '<i class="fas fa-user-lock"></i>' : (($lock) ? '<i class="fas fa-lock-open"></i>' : '<i class="fas fa-lock"></i>'),
                    'title="" id="_lock_unlock' . $id . '" class="__record_lock_unlock btn btn-sm btn-primary" data-blocked="'.$lock.'" data-toggle="tooltip" data-placement="top" data-original-title="' . $textLockUnlock . ' ' . $lockUnlockVoice . '"'
                ) . "\n";
        }
        return $this;
    }

    /**
     * @description Metodo che inserisce il tag HTML di chiusura
     * @return string
     */
    public function render(): string
    {
        $this->htmlDesktop .= $this->tagCloseDesktop;
        $this->htmlMobile .= $this->tagCloseMobile;

        if (!empty($this->editOutsideButton)) {

            $this->htmlDesktop = '
            <div class="dropdown-btn-desktop">
                <div class="d-flex g" style="gap: 0.3rem;">
                    ' . $this->editOutsideButton . '
                    ' . $this->htmlDesktop . '
                </div>
            </div>';
        }

        return $this->htmlDesktop . $this->htmlMobile;
    }

    /**
     * @description Metodo statico che viene richiamato come "Costruttore" al fine si settare i profili per la
     * visualizzazione dei vari bottone nelle action delle tabelle
     *
     * @param array|null $profiles Profili ACL utilizzati per mostrare o meno i pulsanti delle action
     * @return static
     */
    public static function create(?array $profiles = null): static
    {
        return new static($profiles);
    }

    /**
     * @description Metodo che setta le checkbox per la selezione multipla di records su cui effettuare le azioni
     *
     * @param string|null $name         Nome
     * @param int|null    $id           Id del record
     * @param string      $classNameCss Classe css
     * @return string
     */
    public static function checkList(?string $name = 'item[]', ?int $id = null, string $classNameCss = 'checkbox_item'): string
    {
        return form_checkbox(escapeXss($name), (int)$id, false, 'class="' . $classNameCss . '"');
    }
}
