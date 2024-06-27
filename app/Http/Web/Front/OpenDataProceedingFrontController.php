<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\ProceedingsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per i Procedimenti
 */
class OpenDataProceedingFrontController extends BaseFrontController
{
    private array $validation;

    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');

        // Chiamata validatore dai parametri di input valido per tutta la classe.
        $this->validation = (new OpenDataDate())->validateDateAndDiff();

        if (!$this->validation['is_success']) {

            $response = new Response();
            $response->setHeader('Content-Type', 'application/json');
            $response->setStatus($response::BAD);
            $response->body($this->validation['errors']);
            echo $response->send(true);
            die();

        } else {

            header('Cache-Control: max-age=60, must-revalidate');
            header('Set-Cookie: fileDownload=true; path=/');

        }

    }

    /**
     * Metodo download open data per i Provvedimenti,
     * nella sezione Attività e procedimenti->Tipologie di procedimento
     * ID sezione 98
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexProceedingsType(): void
    {

        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = ProceedingsModel::with('responsibles:id,full_name')
            ->with('measure_responsibles:id,full_name')
            ->with('substitute_responsibles:id,full_name')
            ->with('offices_responsibles:id,structure_name')
            ->with('to_contacts:id,full_name')
            ->with('other_structures:id,structure_name')
            ->with('normatives:id,name')
            ->with('attachs')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);

        }
    }

    /**
     * Metodo che genera il csv con gli open data
     * @param $resultContests {Dati pubblicati nella sezione}
     * @param $resultSection  {Dati sulla sezione corrente}
     * @return Void
     * @throws Exception
     */
    private function generateCsv($resultContests = null, $resultSection = null): void
    {
        $fileName = 'Procedimenti - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');

        // Intestazione indice dei dati...
        $headers = array(
            'Nome del procedimento',
            'Responsabile/i di procedimento',
            'Responsabile/i di provvedimento',
            'Responsabile/i sostitutivo',
            'Struttura di riferimento (chi contattare)',
            'Personale di riferimento (chi contattare)',
            'Altre strutture organizzative associate',
            'Descrizione del procedimento',
            'Costi e modalità di pagamento',
            'Riferimenti normativi (diretti)',
            'Termine di conclusione',
            'Link per servizio online',
            'Tempi previsti per attivazione servizio online',
            'url origine ' . $resultSection['name'],
            'Allegati'
        );
        fputcsv($output, $headers, ';', '"');

        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Concateno i responsabili del procedimento
            $responsiblesLists = '';
            if (!empty($r['responsibles'])) {

                foreach ($r['responsibles'] as $responsible) {

                    if(!empty($responsible['full_name'])){
                        $responsiblesLists .= $responsible['full_name'] . ' - [Url]: '
                            . siteUrl('page/4/details/' . $responsible['id'] . '/' . urlTitle($responsible['full_name'])) . "\n";
                    }
                }
            }

            // Concateno i responsabili del provvedimento
            $measureResponsiblesLists = '';
            if (!empty($r['measure_responsibles'])) {

                foreach ($r['measure_responsibles'] as $measureResponsible) {
                    if(!empty($measureResponsible['full_name'])){
                        $measureResponsiblesLists .= $measureResponsible['full_name'] . ' - [Url]: '
                            . siteUrl('page/4/details/' . $measureResponsible['id'] . '/' . urlTitle($measureResponsible['full_name'])) . "\n";
                    }
                }
            }

            // Concateno i responsabili sostitutivi
            $subResponsiblesLists = '';
            if (!empty($r['substitute_responsibles'])) {

                foreach ($r['substitute_responsibles'] as $substituteResponsible) {
                    if(!empty($substituteResponsible['full_name'])){
                        $subResponsiblesLists .= $substituteResponsible['full_name'] . ' - [Url]: '
                            . siteUrl('page/4/details/' . $substituteResponsible['id'] . '/' . urlTitle($substituteResponsible['full_name'])) . "\n";
                    }
                }
            }

            // Concateno le strutture responsabili
            $officesResponsiblesLists = '';
            if (!empty($r['offices_responsibles'])) {

                foreach ($r['offices_responsibles'] as $officesResponsibles) {

                    if(!empty($officesResponsibles['structure_name'])){
                        $officesResponsiblesLists .= $officesResponsibles['structure_name'] . ' - [Url]: '
                            . siteUrl('page/40/details/' . $officesResponsibles['id'] . '/' . urlTitle($officesResponsibles['structure_name'])) . "\n";
                    }
                }
            }

            // Concateno il personale da contattare
            $toContactsLists = '';
            if (!empty($r['to_contacts'])) {

                foreach ($r['to_contacts'] as $toContact) {
                    if(!empty($toContact['full_name'])){
                        $toContactsLists .= $toContact['full_name'] . ' - [Url]: '
                            . siteUrl('page/4/details/' . $toContact['id'] . '/' . urlTitle($toContact['full_name'])) . "\n";
                    }
                }
            }

            // Concateno le strutture responsabili
            $otherStructuresLists = '';
            if (!empty($r['other_structures'])) {

                foreach ($r['other_structures'] as $otherStructures) {
                    if(!empty($otherStructures['structure_name'])){
                        $otherStructuresLists .= $otherStructures['structure_name'] . ' - [Url]: '
                            . siteUrl('page/40/details/' . $otherStructures['id'] . '/' . urlTitle($otherStructures['structure_name'])) . "\n";
                    }
                }
            }

            // Concateno i riferimenti normativi
            $normativesLists = '';
            if (!empty($r['normatives'])) {

                foreach ($r['normatives'] as $normative) {
                    if(!empty($normative['name'])){
                        $normativesLists .= $normative['name'] . ' - [Url]: '
                            . siteUrl('page/24/details/' . $normative['id'] . '/' . urlTitle($normative['name'])) . "\n";
                    }
                }
            }

            // Concateno gli allegati
            $attachLists = '';
            //Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {

                foreach ($r['attachs'] as $attach) {
                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";
                }
            }

            // Genero l'indice dell'array
            $data = [
                // Ragione sociale
                !empty($r['name']) ? $r['name'] : null,
                // Responsabili procedimento
                trim($responsiblesLists),
                // Responsabili provvedimento
                trim($measureResponsiblesLists),
                // Responsabili sostitutivi
                trim($subResponsiblesLists),
                // Struttura di riferimento
                trim($officesResponsiblesLists),
                // Personale da contattare
                trim($toContactsLists),
                // Altre strutture
                trim($otherStructuresLists),
                // Descrizione del procedimento
                !empty($r['description']) ? S::chartsEntityDecode(S::stripTags($r['description'])): null ,
                // Costi e modalità di pagamento
                !empty($r['costs']) ?  S::chartsEntityDecode(S::stripTags($r['costs'])): null,
                // Riferimenti normativi (diretti)
                trim($normativesLists),
                // Termine di conclusione
                !empty($r['deadline']) ? $r['deadline'] : null,
                // Link per servizio online
                !empty($r['url_service']) ? $r['url_service'] : null,
                // Tempi previsti per attivazione servizio online
                !empty($r['service_time']) ? $r['service_time'] : null,
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($r['name'])),
                // Allegati
                trim($attachLists)
            ];
            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
