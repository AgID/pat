<?
// includo i file con la configurazione e con le classi
require_once('./classi/database.php');

class ckstyles {

    var $output = "";

    function  ckstyles($idEditor = -1, $idStile = 0) {
        global $dati_db, $database, $configurazione, $templateScelto;

        $this->output = "CKEDITOR.addStylesSet('stili_editor',[";

		if($configurazione['stili_editor_template'] and $templateScelto['id'] > 0 and $templateScelto['nome'] != '') {
			if($templateScelto['stili_editor'] == '') {
				$templateScelto['stili_editor'] = 0;
			}
			$sql = "SELECT * FROM ".$dati_db['prefisso']."stili_elementi WHERE id IN (".$templateScelto['stili_editor'].")";
		} else {
	        $sql = "SELECT * FROM ".$dati_db['prefisso']."stili_elementi WHERE (id_elemento=".$idEditor." or id_elemento=0) and famiglia='contenuto' and sotto_famiglia='editor'";
		}

        if( !($result = $database->connessioneConReturn($sql)) ) {
            die("errore in caricamento stili dal db ".$sql);
        }
        if ($database->sqlNumRighe($result) != 0) {
            $stili = $database->sqlArrayAss($result);
        } else {
            $stili = array();
        }


        if (is_array($stili) and count($stili)!= 0) {
            foreach ($stili as $stile) {
                $this->output .= "{ name: '".$stile['nome']."' , element: 'span', attributes: { 'class': 'classEditor".$stile['id']."' } } ,";
            }
        }

        $this->output .= "{ name: 'Immagine a sinistra' , element: 'img', attributes: { style: 'padding: 5px; margin-right: 10px; float:left;display:inline;border: 0px solid #000000;position:relative;".$configurazione['immagineEditorSX']."' } } ,";
        $this->output .= "{ name: 'Immagine a destra' , element: 'img', attributes: { style: 'padding: 5px; margin-left: 10px; float:right;display:inline;border: 0px solid #000000;position:relative;".$configurazione['immagineEditorDX']."' } } ,";
        $this->output .= "{ name: 'Immagine con bordo nero' , element: 'img', attributes: { style: 'border: 1px solid #000000;".$configurazione['immagineEditorBordoNero']."' } } ,";
        $this->output .= "{ name: 'Immagine senza bordo' , element: 'img', attributes: { style: 'border: 0px solid #000000;".$configurazione['immagineEditorNoBordo']."' } } ,";
        $this->output .= "{ name: 'Tabella generica' , element: 'table', attributes: { 'class': 'elementoTabella' } }";

        
        $this->output .= "]);";
    }

    function getOutput() {
        return $this->output;
    }

}



