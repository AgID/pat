<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\Validators\OpenDataDate;
use Model\BdncpProcedureModel;
use Model\ContestsActsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Response;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Open data per Procedure Banca Dati Nazionale Contratti Pubblici
 */
class OpenDataBdncpProcedureFrontController extends BaseFrontController
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

            setFileDownloadCookie();

        }
    }

    /**
     * Metodo download open data per le Procedure Banca Dati Nazionale Contratti Pubblici,
     * nella sezione Bandi di Gara e Contratti
     * ID sezione 66
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = BdncpProcedureModel::with('attachs')
            ->filter(
                (Input::get('o', true)),
                Input::get('c', true),
                Input::get('start', true),
                Input::get('end', true),
                Input::get('fase', true)
            );


        $queryContents = $queryContents->where('typology', 'procedure')
            ->with('commission:id,object,name')
            ->with('board:id,object,name')
            ->orderBy('created_at', 'DESC')
            ->skip(boundedQueryInt('skip', 1000000))
            ->take(boundedQueryInt('take', 100000, 100000))
            ->get();

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsv($resultContests, $resultSection);
        }
    }

    /**
     * Metodo download open data per le Procedure Banca Dati Nazionale Contratti Pubblici,
     * nella sezione Bandi di Gara e Contratti
     * ID sezione 66
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function alerts(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = BdncpProcedureModel::with('attachs')
            ->alertFilter(
                (Input::get('o', true)),
                Input::get('start', true),
                Input::get('end', true)
            )
            ->where('typology', 'alert');


        $queryContents = $queryContents->with('relative_bdncp_procedure:id,object,cig')
            ->orderBy('created_at', 'DESC')
            ->skip(boundedQueryInt('skip', 1000000))
            ->take(boundedQueryInt('take', 100000, 100000))
            ->get();

        if (!empty($querySection) && $queryContents->isNotEmpty() ) {

            $resultSection = $querySection->toArray();
            $resultContests = $queryContents->toArray();

            $this->generateCsvAlert($resultContests, $resultSection);
        }
    }



    /**
     * Metodo download open data per le Variazioni in corso d'opera
     * nella sezione Bandi di Gara e Contratti
     * ID sezione 603
     *
     *
     * ---- ATTENZIONE ---- questa funzione recupera i dati sia da BdncpProcedureModel che da ContestsActsModel
     *
     *
     * @url download/open-data/page_id
     * @return void
     * @throws Exception
     */
    public function variations(): void
    {
        // Recupero le informazioni della pagina corrente
        $querySection = SectionsFoModel::select(['id', 'name'])
            ->where('id', uri()->segment(3, 0))
            ->first();

        // Recupero i bandi di concorso attivi
        $queryContents = BdncpProcedureModel::where('typology', 'variation')
            ->variationFilter(
                (Input::get('o', true))
            )
            ->with('attachs');
        $queryContents = $queryContents->with('rel_variation_procedure:id,object,cig')
            ->orderBy('created_at', 'DESC')
            ->get();
        $queryContents = !$queryContents->isEmpty() ? $queryContents->toArray() : [];

        $variationsPre2024 = ContestsActsModel::where('typology', 'variation')
            ->variationFilter(
                (Input::get('o', true))
            )
            ->with('proceedings:id,object')
            ->with('attachs')
            ->orderBy('updated_at', 'DESC')
            ->get();

        $variationsPre2024 = !$variationsPre2024->isEmpty() ? $variationsPre2024->toArray() : [];


        if (!empty($querySection) && ( !empty($queryContents) || !empty($variationsPre2024))) {

            $resultSection = $querySection->toArray();

            $this->generateCsvVariation($queryContents, $resultSection, $variationsPre2024);
        }
    }



    public function alertsExpired(): void
    {
        exit();
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
        //Procedure di gara
        $fileName = $resultSection['name'] . '.csv';
        setFileDownloadCookieCSV($fileName);

        $output = fopen("php://output", 'w');

        //Setto le colonne
        $headers = array(
            'CIG',
            'OGGETTO',
            'LINK BDNCP',
            'NOTE DIBATTITO PUBBLICO',
            'ALLEGATI DIBATTITO PUBBLICO',
            'NOTE DOCUMENTI DI GARA',
            'ALLEGATI DOCUMENTI DI GARA',
            'NOTE COMPOSIZIONE DELLE COMMISSIONI GIUDICATRICI',
            'COMPONENTI DELLE COMMISSIONI GIUDICATRICI',
            'NOTE PARI OPPORTUNITA',
            'ALLEGATI PARI OPPORTUNITA',
            'NOTE PROCEDURE DI AFFIDAMENTO DEI SERVIZI PUBBLICI LOCALI',
            'ALLEGATI PROCEDURE DI AFFIDAMENTO DEI SERVIZI PUBBLICI LOCALI',
            'NOTE COMPOSIZIONE COLLEGIO CONSULTIVO TECNICO',
            'COMPONENTI DEL COMPOSIZIONE COLLEGIO CONSULTIVO TECNICO',
            'NOTE PARI OPPORTUNITA',
            'ALLEGATI PARI OPPORTUNITA',
            'NOTE CONTRATTI GRATUITI E FORME SPECIALI DI PARENTARIATO',
            'ALLEGATI CONTRATTI GRATUITI E FORME SPECIALI DI PARENTARIATO',
            'NOTE AFFIDAMENTI DI SOMMA URGENZA',
            'ALLEGATI AFFIDAMENTI DI SOMMA URGENZA',
            'NOTE PROCEDURA DI AFFIDAMENTO',
            'ALLEGATI PROCEDURA DI AFFIDAMENTO',
            'NOTE',
            'ALLEGATI',
            'ULTIMA MODIFICA',
        );
        fputcsv($output, $headers, ';', '"');

        if (!empty($resultContests)) {

            foreach ($resultContests as $record) {
                $attachLists = [];
                if (!empty($record['attachs'])) {

                    foreach ($record['attachs'] as $attach) {
                        $attachLists [$attach['bdncp_cat']] [] = '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";

                    }
                }

                // Concateno gli incarichi
                $commissions = '';
                if (!empty($record['commission'])) {
                    foreach ($record['commission'] as $commission) {

                        $commissions .= $commission['name'] . ' - [Url]: '
                            . siteUrl('page/46/details/' . $commission['id'] . '/' . urlTitle($commission['name'])) . "\n";

                    }
                }

                // Concateno gli incarichi
                $boards = '';
                if (!empty($record['board'])) {
                    foreach ($record['board'] as $board) {

                        $boards .= $board['name'] . ' - [Url]: '
                            . siteUrl('page/46/details/' . $board['id'] . '/' . urlTitle($board['name'])) . "\n";

                    }
                }

                $updateAt = !empty($record['updated_at']) ? date('d/m/Y', strtotime($record['updated_at'])) : null;

                //Setto i dati da inserire nelle colonne del CSV
                $data = [
                    !empty($record['cig']) ? $record['cig'].' ' : null,
                    !empty($record['object']) ? $record['object'] : null,
                    !empty($record['bdncp_link']) ? $record['bdncp_link'] : null,
                    !empty($record['public_debate_notes']) ? $record['public_debate_notes'] : null,
                    !empty($attachLists['_publicDebate']) ? trim(implode('',$attachLists['_publicDebate'])) : null,
                    !empty($record['notice_documents_notes']) ? $record['notice_documents_notes'] : null,
                    !empty($attachLists['_noticeDocuments']) ? trim(implode('',$attachLists['_noticeDocuments'])) : null,
                    !empty($record['judging_commission_notes']) ? $record['judging_commission_notes'] : null,
                    trim($commissions),
                    !empty($record['equal_opportunities_af_notes']) ? $record['equal_opportunities_af_notes'] : null,
                    !empty($attachLists['_equalOpportunitiesAf']) ? trim(implode('',$attachLists['_equalOpportunitiesAf'])) : null,
                    !empty($record['local_public_services_notes']) ? $record['local_public_services_notes'] : null,
                    !empty($attachLists['_localPublicServices']) ? trim(implode('',$attachLists['_localPublicServices'])) : null,
                    !empty($record['advisory_board_technical_notes']) ? $record['advisory_board_technical_notes'] : null,
                    trim($boards),
                    !empty($record['equal_opportunities_es_notes']) ? $record['equal_opportunities_es_notes'] : null,
                    !empty($attachLists['_equalOpportunitiesEs']) ? trim(implode('',$attachLists['_equalOpportunitiesEs'])) : null,
                    !empty($record['free_contract_notes']) ? $record['free_contract_notes'] : null,
                    !empty($attachLists['_freeContract']) ? trim(implode('',$attachLists['_freeContract'])) : null,
                    !empty($record['emergency_foster_notes']) ? $record['emergency_foster_notes'] : null,
                    !empty($attachLists['_emergencyFoster']) ? trim(implode('',$attachLists['_emergencyFoster'])) : null,
                    !empty($record['foster_procedure_notes']) ? $record['foster_procedure_notes'] : null,
                    !empty($attachLists['_fosterProcedure']) ? trim(implode('',$attachLists['_fosterProcedure'])) : null,
                    !empty($record['notes']) ? $record['notes'] : null,
                    !empty($attachLists['_generalInfo']) ? trim(implode('',$attachLists['_generalInfo'])) : null,
                    $updateAt,
                ];

                fputcsv($output, $data, ';', '"');
            }
            fclose($output);
        }
    }

    /**
     * Metodo che genera il csv con gli open data
     * @param $resultContests {Dati pubblicati nella sezione}
     * @param $resultSection  {Dati sulla sezione corrente}
     * @return Void
     * @throws Exception
     */
    // non mettere private
    protected function generateCsvAlert($resultContests = null, $resultSection = null): void
    {
        //Procedure di gara
        $fileName = $resultSection['name'] . '.csv';
        setFileDownloadCookieCSV($fileName);

        $output = fopen("php://output", 'w');

        //Setto le colonne
        $headers = array(
            'OGGETTO',
            'DATA AVVISO',
            'PROCEDURA RELATIVA',
            'NOTE',
            'ALLEGATI',
            'ULTIMA MODIFICA',
            'url origine ' . $resultSection['name'],
        );
        fputcsv($output, $headers, ';', '"');

        if (!empty($resultContests)) {

            foreach ($resultContests as $record) {
                // Concateno gli allegati
                $attachLists = '';
                if (!empty($record['attachs'])) {

                    foreach ($record['attachs'] as $attach) {
                        $attachLists .= '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";
                    }
                }

                $relativeProcedure = !empty($record['relative_bdncp_procedure'])
                    ? '['. $record['relative_bdncp_procedure']['cig'] .'] - ' . $record['relative_bdncp_procedure']['object'] . ' - [URL]: ' . siteUrl('page/588/details/'.$record['relative_bdncp_procedure']['id'].'/'.urlTitle($record['relative_bdncp_procedure']['object']))
                    : '';

                $updateAt = !empty($record['updated_at']) ? date('d/m/Y', strtotime($record['updated_at'])) : null;

                //Setto i dati da inserire nelle colonne del CSV
                $data = [
                    //Oggetto avviso
                    !empty($record['object']) ? $record['object'] : null,
                    // Data avviso
                    !empty($record['alert_date']) ? date('d/m/Y', strtotime($record['alert_date'])) : null,
                    //Procedura relativa
                    $relativeProcedure,
                    //Note
                    !empty($record['notes']) ? $record['notes'] : null,
                    //Allegati
                    trim($attachLists),
                    //Data di ultimo aggiornamento
                    $updateAt,
                    // Url origine record
                    siteUrl('page/' . $resultSection['id'] . '/details/' . $record['id'] . '/' . urlTitle($record['object'])),
                ];

                fputcsv($output, $data, ';', '"');
            }
            fclose($output);
        }
    }

    private function generateCsvVariation($resultContests, $resultSection, $variationPre2024)
    {
        //Procedure di gara
        $fileName = 'Varianti in corso d opera.csv';
        setFileDownloadCookieCSV($fileName);

        $output = fopen("php://output", 'w');

        //Setto le colonne
        $headers = array(
            'OGGETTO',
            'PROCEDURE RELATIVE',
            'NOTE',
            'ALLEGATI',
            'ULTIMA MODIFICA',
            'url origine ' . $resultSection['name'],
        );
        fputcsv($output, $headers, ';', '"');

        if (!empty($resultContests)) {

            foreach ($resultContests as $record) {
                // Concateno gli allegati
                $attachLists = '';
                if (!empty($record['attachs'])) {

                    foreach ($record['attachs'] as $attach) {
                        $attachLists .= '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";
                    }
                }

                $relativeProcedures = "";
                if(!empty($record['rel_variation_procedure'])){
                    foreach ($record['rel_variation_procedure'] as $proc){
                        $relativeProcedures .= '['. $proc['cig'] .'] - ' . $proc['object'] . ' - [URL]: ' . siteUrl('page/10/details/'.$proc['id'].'/'.urlTitle($proc['object']));
                    }
                }

                $updateAt = !empty($record['updated_at']) ? date('d/m/Y', strtotime($record['updated_at'])) : null;

                //Setto i dati da inserire nelle colonne del CSV
                $data = [
                    //Oggetto avviso
                    !empty($record['object']) ? $record['object'] : null,
                    //Procedura relativa
                    $relativeProcedures,
                    //Note
                    !empty($record['notes']) ? $record['notes'] : null,
                    //Allegati
                    trim($attachLists),
                    //Data di ultimo aggiornamento
                    $updateAt,
                    // Url origine record
                    siteUrl('page/' . $resultSection['id'] . '/details/' . $record['id'] . '/' . urlTitle($record['object'])),
                ];

                fputcsv($output, $data, ';', '"');
            }

        }

        if (!empty($variationPre2024)) {

            foreach ($variationPre2024 as $record) {
                // Concateno gli allegati
                $attachLists = '';
                if (!empty($record['attachs'])) {

                    foreach ($record['attachs'] as $attach) {
                        $attachLists .= '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";
                    }
                }

                $relativeProcedures = "";
                if(!empty($record['proceedings'])){
                    foreach ($record['proceedings'] as $proc){
                        $relativeProcedures .= '[' . $proc['object'] . ' - [URL]: ' . siteUrl('page/112/details/'.$proc['id'].'/'.urlTitle($proc['object']));
                    }
                }

                $updateAt = !empty($record['updated_at']) ? date('d/m/Y', strtotime($record['updated_at'])) : null;

                //Setto i dati da inserire nelle colonne del CSV
                $data = [
                    //Oggetto avviso
                    !empty($record['object']) ? $record['object'] : null,
                    //Procedura relativa
                    $relativeProcedures,
                    //Note
                    !empty($record['details']) ? $record['details'] : null,
                    //Allegati
                    trim($attachLists),
                    //Data di ultimo aggiornamento
                    $updateAt,
                    // Url origine record
                    siteUrl('page/' . $resultSection['id'] . '/details/' . $record['id'] . '/' . urlTitle($record['object'])),
                ];

                fputcsv($output, $data, ';', '"');
            }

        }


        fclose($output);
    }
}
