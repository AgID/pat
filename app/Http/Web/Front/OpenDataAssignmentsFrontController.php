<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Validators\OpenDataDate;
use Model\AssignmentsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per gli incarichi
 */
class OpenDataAssignmentsFrontController extends BaseFrontController
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

        // Chiamata validatore dai parametri di input valido per tutta la classe
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
     * Metodo download open data per gli Incarichi,
     * nella sezione Pagamenti dell'amministrazione->Dati sui pagamenti->Pagamenti di Consulenti e collaboratori
     * ID sezione 156
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexPaymentsConsultantsCollaborators(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = AssignmentsModel::where('object_assignments.typology', 'liquidation')
            ->where('object_assignments.dirigente', '!=', 1)
            ->orWhereNull('object_assignments.dirigente')
            ->whereHas('related_assignment', function ($query) {
                $query->where('assignment_type', '!=', '1');
            })
            ->with(['related_assignment' => function ($query) {
                $query->select(['id', 'name', 'object', 'assignment_type', 'object_structures_id']);
                $query->with('structure:id,structure_name');
            }])
            ->with('structure:id,structure_name')
            ->with('attachs')
            ->orderBy('object_assignments.liquidation_date')
            ->skip(Input::get('skip'))
            ->take(Input::get('take'))
            ->get();

        if (!empty($querySection) && !empty($queryContents)) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $fileName = 'Consulenti e collaboratori - ' . $resultSection['name'] . '.csv';
            header('Content-Type: text/csv; charset= utf-8');
            header('Cache-Control: no-store, no-cache');
            header("Content-Disposition: attachment; filename=" . $fileName);
            $output = fopen("php://output", 'w');

            $headers = array(
                'Nominativo',
                'Oggetto',
                'Tipo di incarico',
                'Incarico amministrativo di vertice o dirigenziale',
                'Struttura organizzativa responsabile',
                'Compenso erogato',
                'Anno compenso erogato',
                'Note (incarichi, cariche, altre attività)',
                'url origine ' . $resultSection['name'],
                'Allegati'
            );
            fputcsv($output, $headers, ';', '"');

            // Creazione delle celle per i dati in formato CSV
            foreach ($resultContests as $r) {

                // Tipo incarico
                $assignmentType = !empty($r['related_assignment']['assignment_type'])
                    ? $r['related_assignment']['assignment_type']
                    : $r['assignment_type'];

                // Oggetto incarico
                $assignmentObject = !empty($r['related_assignment']['object'])
                    ? $r['related_assignment']['object']
                    : $r['object'];

                // Nominativo incarico
                $name = !empty($r['related_assignment']['name'])
                    ? $r['related_assignment']['name']
                    : $r['name'];

                $structure = !empty($r['related_assignment']['structure'])
                    ? $r['related_assignment']['structure']
                    : $r['structure'];

                $structure = !empty($structure) ?
                    $structure['structure_name'] . ' - ' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name']))
                    : '';

                //Concateno gli allegati
                $attachLists = '';
                if (!empty($r['attachs'])) {

                    foreach ($r['attachs'] as $attach) {

                        $attachLists .= '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";

                    }
                }

                $data = [
                    // Nominativo
                    !empty($name) ? $name : null,
                    // Oggetto
                    !empty($assignmentObject) ? $assignmentObject : null,
                    // Tipo incarico
                    $assignmentType == 1
                        ? 'Incarichi retribuiti e non retribuiti dei propri dipendenti'
                        : 'Incarichi retribuiti e non retribuiti affidati a soggetti esterni',
                    // Incarico amministrativo di vertice o dirigenziale
                    'no',
                    // Struttura organizzativa responsabile
                    !empty($structure) ? $structure : null,
                    //  Compenso erogato(liquidazione)
                    !empty($r['compensation_provided']) ? '€ ' . S::currency($r['compensation_provided'], 2, ',', '.') : null,
                    // Anno compenso erogato(anno liquidazione)
                    !empty($r['liquidation_year']) ? $r['liquidation_year'] : null,
                    // Note
                    !empty($r['notes']) ? S::chartsEntityDecode(S::stripTags($r['notes'])) : null ,
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

    /**
     * Metodo download open data per gli Incarichi,
     * nella sezione Personale->Incarichi conferiti e autorizzati ai dipendenti (dirigenti e non dirigenti)
     * ID sezione 67
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     * @noinspection PhpUndefinedVariableInspection
     */
    public function indexExecutivesNonExecutives(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = AssignmentsModel::where('typology', 'assignment')
            ->where('assignment_type', '=', '1')
            ->where(
                function ($query) {
                    $query->where('dirigente', '!=', 1)
                        ->orWhereNull('dirigente');
                }
            )
            ->where(
                function ($query) {
                    $query->orWhere('assignment_end', '')
                        ->orWhereNull('assignment_end')
                        ->orWhere('assignment_end', '>=', date('Y-m-d H:i:s'));
                }
            )
            ->with(['related_assignment' => function ($query) {
                $query->select(['id', 'name', 'object', 'assignment_type', 'object_structures_id']);
                $query->with('structure:id,structure_name');
            }])
            ->with('structure:id,structure_name')
            ->with('measures:id,object')
            ->with('attachs')
            ->orderBy('assignment_start', 'DESC')
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
     * Metodo download open data per gli Incarichi,
     * nella sezione Home->Consulenti e Collaboratori
     * ID sezione 3
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     * @noinspection PhpUndefinedVariableInspection
     */
    public function indexConsultantsAndCollaborators(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = AssignmentsModel::where('typology', 'assignment')
            ->where('assignment_type', '!=', '1')
            ->where(
                function ($query) {
                    $query->where('dirigente', '!=', 1)
                        ->orWhereNull('dirigente');
                }
            )
            ->with(['related_assignment' => function ($query) {
                $query->select(['id', 'name', 'object', 'assignment_type', 'object_structures_id']);
                $query->with('structure:id,structure_name');
            }])
            ->with('structure:id,structure_name')
            ->with('measures:id,object')
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
     * Metodo download open data per gli Incarichi,
     * nella sezione Consulenti e collaboratori->Titolari di incarichi di collaborazione o consulenza
     * ID sezione 46
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function indexOfficeHolders(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = AssignmentsModel::where('assignment_type', 2)
            ->where('typology', 'assignment')
            ->where(
                function ($query) {
                    $query->where('dirigente', '!=', 1)->orWhereNull('dirigente');
                }
            )
            ->where(
                function ($query) {
                    $query->where('assignment_end', '>=', date('Y-m-d H:i:s'))->orWhereNull('assignment_end');
                }
            )
            ->with(['related_assignment' => function ($query) {
                $query->select(['id', 'name', 'object', 'assignment_type', 'object_structures_id']);
                $query->with('structure:id,structure_name');
            }])
            ->with('structure:id,structure_name')
            ->with('measures:id,object')
            ->with('attachs')
            ->orderBy('assignment_start', 'DESC')
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
     * Metodo download open data per gli Incarichi,
     * nella sezione Consulenti e collaboratori->Titolari di incarichi di collaborazione o consulenza->Archivio incarichi di collaborazione o consulenza
     * ID sezione 47
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     * @noinspection PhpUndefinedVariableInspection
     */
    public function indexArchiveOfficeHolders(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = AssignmentsModel::where('assignment_type', 2)
            ->where('typology', 'assignment')
            ->where('assignment_end', '<', date('Y-m-d H:i:s'))
            ->with(['related_assignment' => function ($query) {
                $query->select(['id', 'name', 'object', 'assignment_type', 'object_structures_id']);
                $query->with('structure:id,structure_name');
            }])
            ->with('structure:id,structure_name')
            ->with('measures:id,object')
            ->with('attachs')
            ->orderBy('object_assignments.liquidation_date')
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

        $fileName = 'Consulenti e collaboratori - ' . $resultSection['name'] . '.csv';
        header('Set-Cookie: fileDownload=true; path=/');
        header('Cache-Control: max-age=60, must-revalidate, no-store, no-cache');
        header('Content-Type: text/csv; charset= utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName.'"');
        $output = fopen("php://output", 'w');


        $headers = array(
            'Nominativo',
            'Oggetto',
            'Tipo di incarico',
            'Incarico amministrativo di vertice o dirigenziale',
            'Struttura organizzativa responsabile',
            'Inizio incarico',
            'Fine incarico',
            'Compenso',
            'Compenso erogato',
            //'Data compenso erogato',
            'Anno compenso erogato',
            'Componenti variabili del compenso',
            'Ragione dell\'incarico',
            'Note (incarichi, cariche, altre attività)',
            'Estremi atti di conferimento',
            'Provvedimenti associati',
            'url origine ' . $resultSection['name'],
            'Allegati'
        );

        fputcsv($output, $headers, ';', '"');

        $institutionTypeId = patOsInstituteInfo(['institution_type_id']);
        $assignmentTypeMap = ['' => ''] + config('assignmentTypologies', '', 'app');


        // Creazione delle celle per i dati in formato CSV
        foreach ($resultContests as $r) {

            // Tipo incarico
            $assignmentType = !empty($r['related_assignment']['assignment_type'])
                ? $r['related_assignment']['assignment_type']
                : $r['assignment_type'];

            // Oggetto incarico
            $assignmentObject = !empty($r['related_assignment']['object'])
                ? $r['related_assignment']['object']
                : $r['object'];

            // Nominativo incarico
            $name = !empty($r['related_assignment']['name'])
                ? $r['related_assignment']['name']
                : $r['name'];

            $structure = !empty($r['related_assignment']['structure'])
                ? $r['related_assignment']['structure']
                : $r['structure'];
            $structure = $structure['structure_name'] . ' - ' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name']));

            // Concateno le strutture
            $measuresLists = '';
            if (!empty($r['measures'])) {

                foreach ($r['measures'] as $measure) {
                    $measuresLists .= $measure['object'] . ' - [Url]: '
                        . siteUrl('page/9/details/' . $measure['id'] . '/' . urlTitle($measure['object'])) . "\n";
                }
            }

            // Concateno gli allegati
            $attachLists = '';
            if (!empty($r['attachs'])) {
                foreach ($r['attachs'] as $attach) {
                    $attachLists .= '(' . $attach['label'] . ') - Url: '
                        . siteUrl('/download/' . $attach['id']) . "\n";
                }
            }

            $data = [
                // Nominativo
                !empty($name) ? $name : null,
                // Oggetto
                !empty($assignmentObject) ? $assignmentObject : null,
                // Tipo incarico
                !empty($assignmentTypeMap[(int)$assignmentType]) ? $assignmentTypeMap[(int)$assignmentType] : '',
                // Incarico amministrativo di vertice o dirigenziale
                'no',
                // Struttura organizzativa responsabile
                !empty($structure) ? $structure : null,
                // Inizio incarico
                !empty($r['assignment_start']) ? convertDateForCsv($r['assignment_start']) : null,
                // Fine incarico
                !empty($r['assignment_end']) ? convertDateForCsv($r['assignment_end']) : null,
                //  Compenso(incarico)
                !empty($r['compensation']) ? '€ ' . S::currency($r['compensation'], 2, ',', '.') : null,
                //  Compenso erogato(liquidazione)
                !empty($r['compensation_provided']) ? '€ ' . S::currency($r['compensation_provided'], 2, ',', '.') : null,
                // Data compenso erogato(liquidazione)
                //!empty($r['liquidation_date']) ? $r['liquidation_date'] : null,
                // Anno compenso erogato(liquidazione)
                !empty($r['liquidation_year']) ? $r['liquidation_year'] : null,
                // Componenti variabili del compenso
                !empty($r['variable_compensation']) ? $r['variable_compensation'] : null,
                //Ragioni dell'incarico
                !empty($r['assignment_reason']) ? $r['assignment_reason'] : null,
                // Note
                !empty($r['notes']) ? S::chartsEntityDecode(S::stripTags($r['notes'])) : null ,
                // Estremi atti di conferimento
                !empty($r['acts_extremes']) ? S::chartsEntityDecode(S::stripTags($r['acts_extremes'])) : null,
                // Provvedimenti associati
                trim($measuresLists),
                // Url origine dato
                siteUrl('page/' . $resultSection['id'] . '/details/' . $r['id'] . '/' . urlTitle($name)),
                // Allegati
                trim($attachLists)
            ];

            fputcsv($output, $data, ';', '"');
        }
        fclose($output);
    }
}
