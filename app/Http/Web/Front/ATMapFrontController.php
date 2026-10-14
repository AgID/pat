<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\SectionsFoModel;
use System\BaseController;
use System\Breadcrumbs;

class ATMapFrontController extends BaseController
{

    /**
     * @var Breadcrumbs
     */
    protected Breadcrumbs $breadcrumbs;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper(['FrontOffice/Utility', 'url']);
    }

    public function generateATMAP()
    {
        header('Content-Type: application/json; charset=utf-8');

        $instituteInfo = patOsInstituteInfo(['id', 'full_name_institution', 'short_institution_name']);
        $filePath = MEDIA_PATH . $instituteInfo['short_institution_name'] . DIRECTORY_SEPARATOR . "at_map.json";
        $filePathCustomMap = MEDIA_PATH . $instituteInfo['short_institution_name'] . DIRECTORY_SEPARATOR . "custom_at_map.json";

        //Se è stata inserita una At map personalizzata renderizzo quella.
        if (file_exists($filePathCustomMap)) {
            $json = file_get_contents($filePathCustomMap);
            echo $json;
            exit();
        }

        if (is_dir(MEDIA_PATH . $instituteInfo['short_institution_name']) && file_exists($filePath)) {

            $lastModified = filemtime($filePath); // timestamp ultima modifica
            $oneMonthAgo = strtotime('-1 month');

            if ($lastModified > $oneMonthAgo) {
                $json = file_get_contents($filePath);
                echo $json;
                exit();
            }
        }

        //Prendo tutte le sezioni di di sistema
        $sezioni = SectionsFoModel::where('is_system', 1)
            ->pluck('name', 'id')
            ->toArray();

        $mappa = [
            'data_ultima_modifica' => date('d/m/Y'),
        ];

        $mappa['at_art2_bis_c1'] = $this->buildUrlMap($this->mappaC1(), $sezioni);

        $json = json_encode($mappa, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        @file_put_contents($filePath, $json);
        echo $json;

    }

    private function buildUrlMap(array $schemaMap, array $sezioni): array
    {
        $result = [];

        foreach ($schemaMap as $schemaKey => $sectionId) {
            $url = $this->resolveSectionUrl($schemaKey, $sectionId, $sezioni);
            if ($url !== null) {
                $result[$schemaKey] = $url;
            }
        }

        return $result;
    }

    private function resolveSectionUrl(string $schemaKey, int $sectionId, array $sezioni): ?string
    {
        // Nodo amministrazione trasparente obbligatorio ( home )
        if ($sectionId === 0) {
            return rtrim(siteUrl(), '/');
        }

        if (!isset($sezioni[$sectionId])) {
            return null;
        }

        $name = $sezioni[$sectionId];

        return rtrim(siteUrl(), '/') . '/page/' . $sectionId . '/' . urlTitle($name) . '.html';
    }


    private function mappaC1()
    {

        return [

//            'rischi_corruttivi_e_trasparenza' => 0,
//            'piano_integrato_attivita_organizzazione' => 0,
//            'budget' => 0,

            'amministrazione_trasparente' => 0,
            'disposizioni_generali' => 1,
            'organizzazione' => 2,
            'consulenti_collaboratori' => 3,
            'personale' => 4,
            'bandi_di_concorso' => 5,
            'performance' => 6,
            'enti_controllati' => 7,
            'attivita_e_procedimenti' => 8,
            'provvedimenti' => 9,
            'bandi_gara_e_contratti' => 10,
            'sovvenzioni' => 11,
            'bilanci' => 12,
            'beni_immobili' => 13,
            'controlli_e_rilievi' => 14,
            'servizi_erogati' => 15,
            'pagamenti' => 16,
            'opere_pubbliche' => 17,
            'altri_contenuti' => 18,
            'piano_triennale_prevenzione_corruzione_e_trasparenza' => 21,
            'atti_generali' => 23,
            'prevenzione_della_corruzione' => 22,
            'criteri_e_modalita' => 125,
            'atti_di_concessione' => 126,
            'patrimonio_immobiliare' => 133,
            'canoni_locazione_o_affitto' => 135,
            'rappresentazione_grafica' => 95,
            'societa_partecipate' => 91,
            'enti_diritto_privato_controllati' => 93,
            'titolari_incarichi_collaborazione_consulenza' => 46,

            //Si potrebbe mettere anche la 37.
            //"amministativo" è scritto male, ma così è riportato nello schema
            'organi_di_indirizzo_politico_amministativo' => 605,

            'telefono_e_posta_elettronica' => 43,
            'sanzioni_mancata_comunicazione_dati' => 38,
            'articolazione_uffici' => 40,
            'incarichi_amministrativi_di_vertice' => 58,
            'titolari_incarichi_dirigenziali_non_generali' => 60,
            'dirigenti_cessati' => 61,
            'dotazione_organica' => 64,
            'tassi_di_assenza' => 66,
            'incarichi_conferiti_e_autorizzati_ai_dipendenti' => 67,
            'contrattazione_collettiva' => 69,
            'contrattazione_integrativa' => 70,
            'ammontare_complessivo_dei_premi' => 85,
            'dati_relativi_ai_premi' => 86,
            'tipologie_di_procedimento' => 98,
            'provvedimenti_organo_indirizzo_politico' => 103,
            'provvedimenti_dirigenti_amministrativi' => 106,
            'bilancio_preventivo_e_consuntivo' => 130,
            'organi_revisione_amministrativa_e_contabile' => 144,
            'corte_dei_conti' => 145,

            //C'è anche 166 che potrebbe corrispondere
            'nuclei_di_valutazione' => 139,

            'dati_sui_pagamenti' => 154,
            'tempestivita_dei_pagamenti' => 159,
            'IBAN_e_pagamenti_informatici' => 161,
            'accesso_civico' => 175,
            'accessibilita_e_catalogo_dati' => 176,
            'dati_ulteriori' => 177,
            'informazioni_ambientali' => 185,
            'interventi_straordinari' => 187,
            'pianificazione_governo_territorio' => 184,
            'strutture_sanitarie_accreditate' => 186,
            'atti_programmazione_opere_pubbliche' => 167,
            'tempi_costi_opere_pubbliche' => 168,
            'servizi_e_standard_di_qualita' => 147,
            'costi_contabilizzati' => 149,
            'servizi_in_rete' => 151,
            'class_action' => 148,
            'liste_di_attesa' => 150,
        ];
    }


    private function mappaC2(): array
    {
        return [
            'societa_trasparente' => 0,

            'disposizioni_generali' => 1,
            'organizzazione' => 2,
            'consulenti_collaboratori' => 3,
            'personale' => 4,

            'selezione_del_personale' => 5, // ex bandi_di_concorso
            'enti_controllati' => 7,
            'sovvenzioni' => 11,
            'beni_immobili' => 13,
            'opere_pubbliche' => 17,

            'performance' => 6,
            'attivita_e_procedimenti' => 8,
            'bandi_gara_e_contratti' => 10,
            'bilanci' => 12,
            'servizi_erogati' => 15,
            'controlli_e_rilievi' => 14,
            'pagamenti' => 16,
            'altri_contenuti' => 18,
            'informazioni_ambientali' => 185,

            'atti_generali' => 23,
            'piano_triennale_prevenzione_corruzione_e_trasparenza' => 21,
            'titolari_incarichi_collaborazione_consulenza' => 46,

            'reclutamento_del_personale' => 79,
            'societa_partecipate' => 91,
            'enti_diritto_privato_controllati' => 93,
            'rappresentazione_grafica' => 95,

            'criteri_e_modalita' => 125,
            'atti_di_concessione' => 126,

            'patrimonio_immobiliare' => 133,
            'canoni_locazione_o_affitto' => 135,

            'tempi_costi_opere_pubbliche' => 168,
            'atti_programmazione_opere_pubbliche' => 167,

            'organi_di_indirizzo_politico_amministativo' => 605,
            'telefono_e_posta_elettronica' => 43,
            'sanzioni_mancata_comunicazione_dati' => 38,
            'articolazione_uffici' => 40,

            'incarichi_amministrativi_di_vertice' => 58,
            'dirigenti_cessati' => 61,
            'incarichi_conferiti_e_autorizzati_ai_dipendenti' => 67,
            'contrattazione_integrativa' => 70,
            'titolari_incarichi_dirigenziali_non_generali' => 60,
            'dotazione_organica' => 64,
            'tassi_di_assenza' => 66,
            'contrattazione_collettiva' => 69,
            'ammontare_complessivo_dei_premi' => 85,

            'tipologie_di_procedimento' => 98,
            'bilancio' => 12,

            'servizi_e_standard_di_qualita' => 147,
            'class_action' => 148,
            'costi_contabilizzati' => 149,
            'liste_di_attesa' => 150,
            'servizi_in_rete' => 151,

            'nuclei_di_valutazione' => 139,
            'corte_dei_conti' => 145,
            'organi_revisione_amministrativa_e_contabile' => 144,

            'dati_sui_pagamenti' => 154,
            'tempestivita_dei_pagamenti' => 159,
            'IBAN_e_pagamenti_informatici' => 161,

            'prevenzione_della_corruzione' => 22,
            'accessibilita_e_catalogo_dati' => 176,
            'accesso_civico' => 175,
            'dati_ulteriori' => 177,
        ];
    }


    private function mappaC3(): array
    {
        //attualmente non utilizzato
        return [
            'amministrazione_trasparente' => 0,

            'attivita_e_procedimenti' => 8,
            'bandi_gara_e_contratti' => 10,
            'sovvenzioni' => 11,
            'bilanci' => 12,
            'servizi_erogati' => 15,
            'opere_pubbliche' => 17,
            'informazioni_ambientali' => 185,
            'altri_contenuti' => 18,

            'tipologie_di_procedimento' => 98,
            'criteri_e_modalita' => 125,
            'atti_di_concessione' => 126,

            'tempi_costi_opere_pubbliche' => 168,
            'atti_programmazione_opere_pubbliche' => 167,

            'accesso_civico' => 175,
            'dati_ulteriori' => 177,
        ];
    }


}