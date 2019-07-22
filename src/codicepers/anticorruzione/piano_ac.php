<?php
/*
 * Created on 01/dic/2015
 *
 */
if(!function_exists('mime_content_type')) {

	function mime_content_type($filename) {

		$mime_types = array(

				'txt' => 'text/plain',
				'htm' => 'text/html',
				'html' => 'text/html',
				'php' => 'text/html',
				'css' => 'text/css',
				'js' => 'application/javascript',
				'json' => 'application/json',
				'xml' => 'application/xml',
				'swf' => 'application/x-shockwave-flash',
				'flv' => 'video/x-flv',

				// images
				'png' => 'image/png',
				'jpe' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'jpg' => 'image/jpeg',
				'gif' => 'image/gif',
				'bmp' => 'image/bmp',
				'ico' => 'image/vnd.microsoft.icon',
				'tiff' => 'image/tiff',
				'tif' => 'image/tiff',
				'svg' => 'image/svg+xml',
				'svgz' => 'image/svg+xml',

				// archives
				'zip' => 'application/zip',
				'rar' => 'application/x-rar-compressed',
				'exe' => 'application/x-msdownload',
				'msi' => 'application/x-msdownload',
				'cab' => 'application/vnd.ms-cab-compressed',

				// audio/video
				'mp3' => 'audio/mpeg',
				'qt' => 'video/quicktime',
				'mov' => 'video/quicktime',

				// adobe
				'pdf' => 'application/pdf',
				'psd' => 'image/vnd.adobe.photoshop',
				'ai' => 'application/postscript',
				'eps' => 'application/postscript',
				'ps' => 'application/postscript',

				// ms office
				'doc' => 'application/msword',
				'rtf' => 'application/rtf',
				'xls' => 'application/vnd.ms-excel',
				'ppt' => 'application/vnd.ms-powerpoint',

				// open office
				'odt' => 'application/vnd.oasis.opendocument.text',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		$ext = strtolower(array_pop(explode('.',$filename)));
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}
}

require_once 'vendor/phpoffice/phpword/bootstrap.php';

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

//inizializzazione stili
if(file_exists('codicepers/anticorruzione/enti/'.$entePubblicato['nome_breve_ente'].'_stili.php')) {
	//stili personalizzati
	include('codicepers/anticorruzione/enti/'.$entePubblicato['nome_breve_ente'].'_stili.php');
} else {
	//stili default
	include('codicepers/anticorruzione/stili.php');
}

$section = $phpWord->addSection(array('pageNumberingStart' => 1));

$footer = $section->addFooter();

$footer->addPreserveText('Pagina {PAGE} di {NUMPAGES}', null, array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER));

$section->addText(
    htmlspecialchars(
        'Indice'
    ),
    'titolo2'
);

$section->addTOC($tocStyle);

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Premessa'
    ),
    2
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['premessa']);

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Processo di adozione del piano'
    ),
    2
);
$section->addTitle(
    htmlspecialchars(
        'Data e documento di approvazione del Piano da parte degli organi di indirizzo politico amministrativo'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pa_descrizione']);

$section->addTextBreak(1);

$provvedimento = mostraDatoOggetto($istanzaOggetto['pa_provvedimento'], 28, '*');
if($provvedimento['id']) {
	$textRun = $section->createTextRun();
	 
	$textRun->addText(
	    htmlspecialchars(
	        'Documento di approvazione del piano da parte degli organi di indirizzo politico amministrativo: '
	    ),
	    array('bold' => true)
	);
	$textRun->addText(
	    htmlspecialchars(
	        html_entity_decode($provvedimento['oggetto'])
	    )
	);
}
if($provvedimento['data']) {
	$textRun = $section->createTextRun();
	 
	$textRun->addText(
	    htmlspecialchars(
	        'Data: '
	    ),
	    array('bold' => true)
	);
	$textRun->addText(
	    htmlspecialchars(
	        date('d/m/Y', $provvedimento['data'])
	    )
	);
}
$strutturaProvvedimento = mostraDatoOggetto($provvedimento['struttura'], 13, '*');
if($strutturaProvvedimento['id']) {
	$textRun = $section->createTextRun();
	 
	$textRun->addText(
	    htmlspecialchars(
	        'Struttura Organizzativa Responsabile: '
	    ),
	    array('bold' => true)
	);
	$textRun->addText(
	    htmlspecialchars(
	        html_entity_decode($strutturaProvvedimento['nome_ufficio'])
	    )
	);
}
if($provvedimento['contenuto']) {
	$textRun = $section->createTextRun();
	 
	$textRun->addText(
	    htmlspecialchars(
	        'Contenuto del provvedimento: '
	    ),
	    array('bold' => true)
	);
	\PhpOffice\PhpWord\Shared\Html::addHtml($section, $provvedimento['contenuto']);
}
if($provvedimento['estremi']) {
	$textRun = $section->createTextRun();
	 
	$textRun->addText(
	    htmlspecialchars(
	        'Estremi documenti principali: '
	    ),
	    array('bold' => true)
	);
	$textRun->addText(
	    htmlspecialchars(
	        html_entity_decode($provvedimento['estremi'])
	    )
	);
}

$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Soggetti coinvolti'
    ),
    3
);
$section->addTitle(
    htmlspecialchars(
        'Individuazione degli attori interni all\'amministrazione che hanno partecipato alla predisposizione del Piano nonche\' dei canali e degli strumenti di partecipazione'
    ),
    4
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pa_si_descrizione']);

if($istanzaOggetto['pa_si_soggetti']) {
	 
	$section->addText(
	    htmlspecialchars(
	        'Attori interni all\'amministrazione che hanno partecipato alla predisposizione del piano: '
	    ),
	    array('bold' => true)
	);
	
	$textRun = $section->createTextRun();
	
	$si = json_decode($istanzaOggetto['pa_si_soggetti']);
	$i = 0;
	foreach((array) $si as $s) {
		if($s->nome != '') {
			$textRun->addText(
			    htmlspecialchars(
			        html_entity_decode($s->nome)
			    ),
	    		array('bold' => true)
			);
		}
		if($s->organo_politico != '') {
			$textRun->addText(
			    htmlspecialchars(
			        ' - '.html_entity_decode($s->organo_politico)
			    )
			);
		}
		if($s->ruolo != '') {
			$textRun->addTextBreak(1);
			$textRun->addText(
			    htmlspecialchars(
			        'Ruolo: '.html_entity_decode($s->ruolo)
			    )
			);
		}
		if($s->strutture != '') {
			$textRun->addTextBreak(1);
			$textRun->addText(
			    htmlspecialchars(
			        'Strutture organizzative: '.html_entity_decode($s->strutture)
			    )
			);
		}
		$textRun->addTextBreak(2);
	}
}

if($istanzaOggetto['pa_si_uffici']) {
	 
	$section->addText(
	    htmlspecialchars(
	        'Strutture organizzative che hanno partecipato alla predisposizione del piano: '
	    ),
	    array('bold' => true)
	);
	
	$textRun = $section->createTextRun();
	
	$si = json_decode($istanzaOggetto['pa_si_uffici']);
	$i = 0;
	foreach((array) $si as $s) {
		if($s->ufficio != '') {
			$textRun->addText(
			    htmlspecialchars(
			        html_entity_decode($s->ufficio)
			    ),
	    		array('bold' => true)
			);
		}
		if($s->responsabile != '') {
			$textRun->addTextBreak(1);
			$textRun->addText(
			    htmlspecialchars(
			        'Responsabile: '.html_entity_decode($s->responsabile)
			    )
			);
		}
		$textRun->addTextBreak(2);
	}
}

$section->addTitle(
    htmlspecialchars(
        'Individuazione degli attori esterni all\'amministrazione che hanno partecipato alla predisposizione del Piano nonche\' dei canali e degli strumenti di partecipazione'
    ),
    4
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pa_se_descrizione']);

if($istanzaOggetto['pa_se_soggetti']) {
	 
	$section->addText(
	    htmlspecialchars(
	        'Attori esterni all\'amministrazione che hanno partecipato alla predisposizione del piano: '
	    ),
	    array('bold' => true)
	);
	
	$textRun = $section->createTextRun();
	
	$si = json_decode($istanzaOggetto['pa_se_soggetti']);
	$i = 0;
	foreach((array) $si as $s) {
		if($s->nome != '') {
			$textRun->addText(
			    htmlspecialchars(
			        html_entity_decode($s->nome)
			    ),
	    		array('bold' => true)
			);
		}
		if($s->ruolo != '') {
			$textRun->addTextBreak(1);
			$textRun->addText(
			    htmlspecialchars(
			        'Ruolo: '.html_entity_decode($s->ruolo)
			    )
			);
		}
		$textRun->addTextBreak(2);
	}
}

$section->addTitle(
    htmlspecialchars(
        'Canali, strumenti e iniziative di comunicazione dei contenuti del piano'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pa_canali_descrizione']);

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Gestione del Rischio'
    ),
    2
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['gr_descrizione']);

$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Le aree di Rischio'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['gr_aree_descrizione']);


if($istanzaOggetto['gr_aree_aree'] != '') {
	
	$arrayRischi = array();
	$recs = json_decode($istanzaOggetto['gr_aree_aree']);
	foreach((array) $recs as $s) {
		$arrayRischi[$s->area][$s->sottoarea][] = $s;
	}
	
	$i = 0;
	foreach((array) $arrayRischi as $area => $s) {
		$section->addText(
		    htmlspecialchars(
		        html_entity_decode($area)
		    ),
		    'titolo4'
		);
		foreach((array) $s as $sottoarea => $r) {
			$section->addText(
			    htmlspecialchars(
			        html_entity_decode($sottoarea)
			    ),
			    'testo'
			);
			$table = $section->addTable('table1');
			$table->addRow(900);
			$table->addCell(3000, $styleCell)->addText(htmlspecialchars('SETTORE'), $arrayStili['bold']);
			$table->addCell(3000, $styleCell)->addText(htmlspecialchars('ATTIVITA\''), $arrayStili['bold']);
			$table->addCell(3000, $styleCell)->addText(htmlspecialchars('RISCHIO'), $arrayStili['bold']);
			$table->addCell(1000, $styleCell)->addText(htmlspecialchars('CLASSIFICAZIONE RISCHIO'), $arrayStili['bold']);
			
			foreach((array) $r as $rischio) {
				$table->addRow();
				$table->addCell(3000)->addText(htmlspecialchars(html_entity_decode($rischio->settore)));
				$cell = $table->addCell(3000);
				\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $rischio->attivita);
				$cell = $table->addCell(3000);
				\PhpOffice\PhpWord\Shared\Html::addHtml($cell, $rischio->rischio);
				$table->addCell(1000)->addText(htmlspecialchars(html_entity_decode($rischio->classificazione_rischio)));
			}
			$section->addTextBreak(1);
		}
		$section->addTextBreak(1);
	}
	
}

$section->addTitle(
    htmlspecialchars(
        'Indicazione della metodologia utilizzata per effettuare la valutazione del rischio'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['gr_metodo_valutazione']);

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Misure obbligatorie ed ulteriori'
    ),
    2
);

$section->addText(
    htmlspecialchars(
        'Piano triennale di prevenzione della corruzione - Adozione del P.T.P.C.'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_ptpc_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Modelli di prevenzione della corruzione'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_mpc_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Adempimenti di trasparenza'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_at_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Codice di comportamento'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_cc_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Rotazione del personale'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_rp_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Obbligo di astensione in caso di conflitto di interesse'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_oaci_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Conferimento e autorizzazione incarichi'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_cai_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Inconferibilita\' per incarichi dirigenziali'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_iid_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Imcompatibilita\' per particolari posizioni dirigenziali'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_ippd_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Attivita\' successive alla cessazione dal servizio'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_ascs_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Formazione di commissioni, assegnazione agli uffici, conferimento di incarichi in caso di condanna per delitti contro la P.A.'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_fcau_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Tutela del dipendente pubblico che segnala gli illeciti'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_dpsi_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Formazione del personale'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_fdp_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Patti di integrita\' negli affidamenti'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_pia_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Azione di sensibilizzazione e rapporto con la societa\' civile'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_asrsc_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Monitoraggio dei tempi procedimentali'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_mtp_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Monitoraggio dei rapporti amministrazione/soggetti esterni'
    ),
    'titolo3'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['mo_mrase_descrizione']);

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Piano Triennale della Trasparenza'
    ),
    2
);

$section->addTitle(
    htmlspecialchars(
        'Introduzione: organizzazione e funzioni dell\'amministratore'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['ptt_intro_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Le principali novita\''
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['ptt_pn_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Procedimento di elaborazione e adozione del Programma'
    ),
    3
);
$section->addText(
    htmlspecialchars(
        'Obiettivi strategici in materia di trasparenza posti dagli organi di vertice negli atti di indirizzo'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['peap_os_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Collegamenti con il Piano della performance o con analoghi strumenti di programmazione previsti da normative di settore'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['peap_cp_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Indicazione dei dirigenti coinvolti per l\'individuazione dei contenuti del Programma'
    ),
    'titolo4'
);
$textRun = $section->createTextRun();
	
$si = json_decode($istanzaOggetto['peap_dirigenti']);
$i = 0;
foreach((array) $si as $s) {
	if($s->nome != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->nome)
		    ),
    		array('bold' => true)
		);
	}
	if($s->ruolo != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Ruolo: '.html_entity_decode($s->ruolo)
		    )
		);
	}
	if($s->strutture != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Strutture organizzative: '.html_entity_decode($s->strutture)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addText(
    htmlspecialchars(
        'Indicazione degli uffici coinvolti per l\'individuazione dei contenuti del Programma'
    ),
    'titolo4'
);
$textRun = $section->createTextRun();

$si = json_decode($istanzaOggetto['peap_uffici']);
$i = 0;
foreach((array) $si as $s) {
	if($s->ufficio != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->ufficio)
		    ),
    		array('bold' => true)
		);
	}
	if($s->responsabile != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Responsabile: '.html_entity_decode($s->responsabile)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addText(
    htmlspecialchars(
        'Modalita\' di coinvolgimento degli stakeholder e i risultati di tale coinvolgimento'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['peap_mcs_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Termini e le modalita\' di adozione del Programma da parte degli organi di vertice'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['peap_tma_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Iniziative di comunicazione della trasparenza'
    ),
    3
);

$section->addText(
    htmlspecialchars(
        'Iniziative e strumenti di comunicazione per la diffusione dei contenuti del Programma e dei dati pubblicati'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['ict_isc_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Organizzazione e risultati attesi delle Giornate della trasparenza'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['ict_ora_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Processo di attuazione del Programma'
    ),
    3
);

$section->addText(
    htmlspecialchars(
        'Individuazione dei dirigenti responsabili della trasmissione dei dati'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_idr_descrizione']);
$section->addTextBreak(1);

$textRun = $section->createTextRun();
	
$si = json_decode($istanzaOggetto['pap_idr_dirigenti']);
$i = 0;
foreach((array) $si as $s) {
	if($s->nome != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->nome)
		    ),
    		array('bold' => true)
		);
	}
	if($s->ruolo != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Ruolo: '.html_entity_decode($s->ruolo)
		    )
		);
	}
	if($s->strutture != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Strutture organizzative: '.html_entity_decode($s->strutture)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addText(
    htmlspecialchars(
        'Individuazione dei dirigenti responsabili della pubblicazione e dell\'aggiornamento dei dati'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_idrp_descrizione']);
$section->addTextBreak(1);

$textRun = $section->createTextRun();
	
$si = json_decode($istanzaOggetto['pap_idrp_dirigenti']);
$i = 0;
foreach((array) $si as $s) {
	if($s->nome != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->nome)
		    ),
    		array('bold' => true)
		);
	}
	if($s->ruolo != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Ruolo: '.html_entity_decode($s->ruolo)
		    )
		);
	}
	if($s->strutture != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Strutture organizzative: '.html_entity_decode($s->strutture)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addText(
    htmlspecialchars(
        'Individuazione di eventuali referenti per la trasparenza e specificazione delle modalita\' di coordinamento con il Responsabile della trasparenza'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_iert_descrizione']);
$section->addTextBreak(1);

$textRun = $section->createTextRun();
	
$si = json_decode($istanzaOggetto['pap_iert_referenti']);
$i = 0;
foreach((array) $si as $s) {
	if($s->nome != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->nome)
		    ),
    		array('bold' => true)
		);
	}
	if($s->ruolo != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Ruolo: '.html_entity_decode($s->ruolo)
		    )
		);
	}
	if($s->strutture != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Strutture organizzative: '.html_entity_decode($s->strutture)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addText(
    htmlspecialchars(
        'Misure organizzative volte ad assicurare la regolarita\' e la tempestivita\' dei flussi informativi'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_mo_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Misure di monitoraggio e di vigilanza sull\'attuazione degli obblighi di trasparenza a supporto dell\'attivita\' di controllo dell\'adempimento da parte del responsabile della trasparenza'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_mmv_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Strumenti e tecniche di rilevazione dell\'effettivo utilizzo dei dati da parte degli utenti della sezione "Amministrazione Trasparente"'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_str_descrizione']);
$section->addTextBreak(1);

$section->addText(
    htmlspecialchars(
        'Misure per assicurare l\'efficacia dell\'istituto dell\'accesso civico'
    ),
    'titolo4'
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_mae_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Dati ulteriori'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['pap_de_descrizione']);

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Coordinamento con il ciclo delle performance'
    ),
    2
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['ccp_descrizione']);

$textRun = $section->createTextRun();
	
$si = json_decode($istanzaOggetto['ccp_regolamenti']);
$i = 0;
foreach((array) $si as $s) {
	if($s->nome != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->nome)
		    ),
    		array('bold' => true)
		);
	}
	if($s->strutture != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Strutture organizzative: '.html_entity_decode($s->strutture)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Formazione'
    ),
    2
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['form_intro_descrizione']);
$textRun->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Individuazione dei soggetti cui viene erogata la formazione in tema di anticorruzione'
    ),
    3
);

$istanzaOggetto['form_personale'] = trim(preg_replace('/\s+/', ' ', $istanzaOggetto['form_personale']));
$si = json_decode($istanzaOggetto['form_personale']);
$i = 0;
foreach((array) $si as $s) {
	$textRun = $section->createTextRun();
	if($i > 0) {
		$textRun->addTextBreak(2);
	}
	if($s->nome != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->nome)
		    ),
    		array('bold' => true)
		);
	}
	if($s->ruolo != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Ruolo: '.html_entity_decode($s->ruolo)
		    )
		);
	}
	if($s->strutture != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Strutture organizzative: '.html_entity_decode($s->strutture)
		    )
		);
	}
	if($s->formazione != '') {
		$form = preg_replace('~>\s+<~', '><', $s->formazione);
		\PhpOffice\PhpWord\Shared\Html::addHtml($section, trim(preg_replace('/\s+/', ' ', $form)));
	}
	$i++;
}

$section->addTitle(
    htmlspecialchars(
        'Individuazione dei soggetti che erogano la formazione in tema di anticorruzione'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['form_sef_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Indicazione dei contenuti della formazione in tema di anticorruzione'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['form_cfa_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Indicazione di canali e strumenti di erogazione della formazione in tema di anticorruzione'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['form_cse_descrizione']);
$section->addTextBreak(1);

$section->addTitle(
    htmlspecialchars(
        'Quantificazione di ore/giornate dedicate alla formazione in tema di anticorruzione'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['form_qog_descrizione']);
$section->addTextBreak(1);


$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Codici di comportamento adottati'
    ),
    2
);
$section->addTitle(
    htmlspecialchars(
        'Adozione delle integrazioni al codice di comportamento dei dipendenti pubblici'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['cca_ai_descrizione']);

$textRun = $section->createTextRun();
	
$si = json_decode($istanzaOggetto['cca_ai_regolamenti']);
$i = 0;
foreach((array) $si as $s) {
	if($s->nome != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->nome)
		    ),
    		array('bold' => true)
		);
	}
	if($s->strutture != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Strutture organizzative: '.html_entity_decode($s->strutture)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addTitle(
    htmlspecialchars(
        'Indicazione dei meccanismi di denuncia delle violazioni del codice di comportamento'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['cca_imd_descrizione']);

$section->addTitle(
    htmlspecialchars(
        'Indicazione dell\'ufficio competente a emanare pareri sulla applicazione del codice di comportamento'
    ),
    3
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['cca_iuc_descrizione']);

$textRun = $section->createTextRun();

$si = json_decode($istanzaOggetto['cca_iuc_uffici']);
$i = 0;
foreach((array) $si as $s) {
	if($s->ufficio != '') {
		$textRun->addText(
		    htmlspecialchars(
		        html_entity_decode($s->ufficio)
		    ),
    		array('bold' => true)
		);
	}
	if($s->responsabile != '') {
		$textRun->addTextBreak(1);
		$textRun->addText(
		    htmlspecialchars(
		        'Responsabile: '.html_entity_decode($s->responsabile)
		    )
		);
	}
	$textRun->addTextBreak(2);
}

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Altre iniziative'
    ),
    2
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['ai_descrizione']);

$section->addPageBreak();

$section->addTitle(
    htmlspecialchars(
        'Sanzioni emanate'
    ),
    2
);
\PhpOffice\PhpWord\Shared\Html::addHtml($section, $istanzaOggetto['se_descrizione']);


$fileName = 'piano-anticorruzione.docx';
$tempFile = 'tmp/'.$fileName;


// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($tempFile);
/*
// Saving the document as ODF file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
$objWriter->save($tempFile);
*/

//header("Content-Disposition: attachment; filename='".$fileName."'");
//readfile($tempFile); // or echo file_get_contents($temp_file);
//unlink($tempFile);

// leggo ed invio il file in download
header('HTTP/1.1 200 OK');
header('Status: 200 OK');
header("Content-Type: ".mime_content_type($tempFile));
header('Content-disposition: attachment;filename="'.rawurldecode(($fileName))).'"';
header("X-Frame-Options: sameorigin");
@readfile($tempFile);
unlink($tempFile);

/*
// Saving the document as HTML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
$objWriter->save('helloWorld.html');
*/
/* Note: we skip RTF, because it's not XML-based and requires a different example. */
/* Note: we skip PDF, because "HTML-to-PDF" approach is used to create PDF documents. */

?>