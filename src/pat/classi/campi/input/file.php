<? 
			// nominativo
			if ($valoreVero == '') {
				// il campo è vuoto
				if($configurazione['upload_multiplo_oggetto']) {
					
					//upload multiplo
					if($configurazione['upload_multiplo_file'] < 1) {
						$configurazione['upload_multiplo_file'] += 1;
						$configurazione['upload_multiplo_script_init'] = "var dummy , fileUpp , uploadCompletato = 0 ";
						?>
							<link href="moduli/swfupload/css/default.css" rel="stylesheet" type="text/css" />
							<script type="text/javascript" src="moduli/swfupload/swfupload.js"></script>
							<script type="text/javascript" src="moduli/swfupload/simple/js/swfupload.queue.js"></script>
							<script type="text/javascript" src="moduli/swfupload/simple/js/fileprogress.js"></script>
							<script type="text/javascript" src="moduli/swfupload/simple/js/handlers.js"></script>
						<?
					} else {
						$configurazione['upload_multiplo_file'] += 1;
						?>
						<script type="text/javascript">
						//uploadCompletato++;
						</script>
						<?
					}
					$mktime = mktime();
					$configurazione['upload_multiplo_script_init'] .= " ,swfu_".$configurazione['upload_multiplo_file'];
					$configurazione['upload_multiplo_script_func'] .= " swfu_".$configurazione['upload_multiplo_file']." = new SWFUpload({
									flash_url : \"moduli/swfupload/swfupload.swf\",
									upload_url: \"upload.php\",
									post_params: {\"PHPUPSESSID\" : \"".$mktime.$configurazione['upload_multiplo_file']."\" , \"nome\" : \"".$nome."\" , \"".$nome."azione\" : \"aggiungi\" },
									file_size_limit : \"100 MB\",
									file_types : \"*.*\",
									file_types_description : \"Tutti i file\",
									file_upload_limit : 100,
									file_queue_limit : 0,
									custom_settings : {
										progressTarget : \"fsUploadProgress_".$nome."\",
										cancelButtonId : \"btnCancel\",
										divstatus : \"divStatus_".$nome."\",
										uploadInCoda : false
									},
									debug: false,
					
									// Button settings
									button_image_url: \"moduli/swfupload/simple/images/upload_multiplo.png\",
									button_width: 61,
									button_height: 22,
									button_placeholder_id: \"spanButtonPlaceHolder_".$nome."\",
									button_text_style: \".theFont { font-size: 16; }\",
									button_text_left_padding: 12,
									button_text_top_padding: 3,
									
									// The event handler functions are defined in handlers.js
									file_queued_handler : fileQueued,
									file_queue_error_handler : fileQueueError,
									file_dialog_complete_handler : fileDialogComplete,
									upload_start_handler : uploadStart,
									upload_progress_handler : uploadProgress,
									upload_error_handler : uploadError,
									upload_success_handler : uploadSuccess,
									upload_complete_handler : uploadCompleteObject,
									queue_complete_handler : queueComplete	// Queue plugin event
								}); ";
						
						if($configurazione['upload_multiplo_script_obbligatorio_campi'][$nome] == 1) {
							$configurazione['upload_multiplo_script_obbligatorio'] .=  "
								fileUpp = swfu_".$configurazione['upload_multiplo_file'].".getStats().files_queued;
								if (fileUpp== '' || fileUpp== '0') {
									window.alert('Non hai selezionato nessun file da inviare per il campo ".$nomi.".');
									return false;
								}";
						}
						//$configurazione['upload_multiplo_script_obbligatorio'] .= " swfu_".$configurazione['upload_multiplo_file'].".startUpload(); ";
						$configurazione['upload_multiplo_script_obbligatorio'] .= " 
								if(uploadCompletato > 0) {
									swfu_".$configurazione['upload_multiplo_file'].".startUpload();
								} else {
									document.forms.formEditor.submit(); void(0);
								}";
					?>
					
					<div class="fieldset flash" id="fsUploadProgress_<? echo $nome; ?>">
					</div>
					<div style="display:inline;">
						<span id="spanButtonPlaceHolder_<? echo $nome; ?>"></span>
					</div>
					<div id="divStatus_<? echo $nome; ?>" style="display:inline;" class="divstatus">0 File inviati</div>
					<input class="stileForm" type="hidden" name="PHPUPSESSID_<? echo $nome; ?>" size="28" value="<? echo $mktime.$configurazione['upload_multiplo_file']; ?>">
					<input type="hidden" id="<? echo $nome; ?>azione" name="<? echo $nome; ?>azione" value="aggiungi" />
						<? 
					
				} else {
					
					// upload classico
					if ($classeStr == '') {
						$styleStr = "style=\"width:100px !important;margin:0px 6px 0px 0px;\"";
					} else {
						$styleStr = "";
					}
					echo "<input ".$classeStr." ".$evento." type=\"file\" id=\"".$nome."\" name=\"".$nome."\" value=\"\" ".$styleStr." />";
					echo "<input style=\"width:auto !important;margin-left:100px;\" onClick=\"document.getElementById('".$nome."').value = '';\" type=\"button\" value=\"cancella selezionato\" />";
					echo "<input type=\"hidden\" id=\"".$nome."azione\" name=\"".$nome."azione\" value=\"aggiungi\" />";
					
				}
			} else {
				
				if($configurazione['upload_multiplo_oggetto']) {
					if ($classeStr == '') {
						$styleStr = "style=\"width:100px !important;margin:0px 6px 0px 0px;display:none;\"";
					} else {
						$styleStr = "";
					}
					echo "<div>";
					echo "<input type=\"hidden\" id=\"".$nome."cancella\" name=\"".$nome."cancella\" value=\"\" />";
					echo "<input type=\"hidden\" id=\"".$nome."\" name=\"".$nome."\" value=\"".$valoreVero."\" />";
					$files = explode("|", $valoreVero);
					echo "<input type=\"hidden\" id=\"".$nome."numero\" name=\"".$nome."numero\" value=\"".count($files)."\" />";
					$cont = 1;
					foreach ($files as $file) {
						$valoreVero = explode("O__O", $file);
						$valoreVero = $valoreVero[1];
						// il file è in modifica, inserisco anche il bottone di eliminazione del file
	                    echo "<div style=\"padding:6px 0px 6px 0px;\"><span style=\"padding:0px 10px 0px 0px;\">".$valoreVero."</span>";
	                    echo "<input type=\"input\" disabled=\"true\" id=\"".$cont.$nome."\" name=\"".$cont.$nome."\" value=\"File da eliminare\" style=\"display:none;width:84px;margin:0px 10px 6px 0px;\" />";
						
						echo "<span id=\"ancora".$cont.$nome."\"><a class=\"bottoneClassico\" title=\"Elimina il file\" href=\"javascript:cancellaFile".$nome."('".$file."', '".$cont.$nome."');\">
							<img src=\"grafica/admin_skin/classic/file_cancel.gif\" alt=\"Elimina il file\" />Cancella</a></span>
							</div>";
						$cont++;
					}
					// inserisco il codice javascript necessario
					echo "<script type=\"text/javascript\">
					function cancellaFile".$nome."(file, cont) {
						// richiesta di cancellazione per il file
						filecampo = document.getElementById('".$nome."cancella');
						if(filecampo.value == '') {
							filecampo.value = file;
						} else {
							filecampo.value = filecampo.value + '|' + file;
						}
						filecampo = document.getElementById('".$nome."numero');
						filecampo.value = filecampo.value - 1;
						filecampo = 'ancora' + cont;
						filecampo = document.getElementById(filecampo);
						filecampo.style.display = 'none';
						filecampo = document.getElementById(cont);
						filecampo.style.display = 'inline';
					}
					</script>";
					echo"</div>";
					
					// in modifica upload multiplo
					if($configurazione['upload_multiplo_file'] < 1) {
						$configurazione['upload_multiplo_file'] += 1;
						$configurazione['upload_multiplo_script_init'] = "var dummy , fileUpp , uploadCompletato = 0 ";
						?>
							<link href="moduli/swfupload/css/default.css" rel="stylesheet" type="text/css" />
							<script type="text/javascript" src="moduli/swfupload/swfupload.js"></script>
							<script type="text/javascript" src="moduli/swfupload/simple/js/swfupload.queue.js"></script>
							<script type="text/javascript" src="moduli/swfupload/simple/js/fileprogress.js"></script>
							<script type="text/javascript" src="moduli/swfupload/simple/js/handlers.js"></script>
						<?
					} else {
						$configurazione['upload_multiplo_file'] += 1;
						$configurazione['upload_multiplo_script_func'] .= " ";
					}
					$mktime = mktime();
					$configurazione['upload_multiplo_script_init'] .= " ,swfu_".$configurazione['upload_multiplo_file'];
					$configurazione['upload_multiplo_script_func'] .= " swfu_".$configurazione['upload_multiplo_file']." = new SWFUpload({
									flash_url : \"moduli/swfupload/swfupload.swf\",
									upload_url: \"upload.php\",
									post_params: {\"PHPUPSESSID\" : \"".$mktime.$configurazione['upload_multiplo_file']."\" , \"nome\" : \"".$nome."\" , \"".$nome."azione\" : \"nessuna\" },
									file_size_limit : \"100 MB\",
									file_types : \"*.*\",
									file_types_description : \"Tutti i file\",
									file_upload_limit : 100,
									file_queue_limit : 0,
									custom_settings : {
										progressTarget : \"fsUploadProgress_".$nome."\",
										cancelButtonId : \"btnCancel\",
										divstatus : \"divStatus_".$nome."\",
										uploadInCoda : false
									},
									debug: false,
					
									// Button settings
									button_image_url: \"moduli/swfupload/simple/images/upload_multiplo.png\",
									button_width: 61,
									button_height: 22,
									button_placeholder_id: \"spanButtonPlaceHolder_".$nome."\",
									button_text_style: \".theFont { font-size: 16; }\",
									button_text_left_padding: 12,
									button_text_top_padding: 3,
									
									// The event handler functions are defined in handlers.js
									file_queued_handler : fileQueued,
									file_queue_error_handler : fileQueueError,
									file_dialog_complete_handler : fileDialogComplete,
									upload_start_handler : uploadStart,
									upload_progress_handler : uploadProgress,
									upload_error_handler : uploadError,
									upload_success_handler : uploadSuccess,
									upload_complete_handler : uploadCompleteObject,
									queue_complete_handler : queueComplete	// Queue plugin event
								}); ";
						
						if($configurazione['upload_multiplo_script_obbligatorio_campi'][$nome] == 1) {
							$configurazione['upload_multiplo_script_obbligatorio'] .=  "
								fileUpp = swfu_".$configurazione['upload_multiplo_file'].".getStats().files_queued;
								fileCanc = document.getElementById('".$nome."numero').value;
								if (fileUpp== '' || fileUpp== '0') {
									if (fileCanc == '0' || fileCanc == 0) {
										window.alert('Non hai selezionato nessun file da inviare per il campo ".$nomi.".');
										return false;
									}
								}";
						}
						$configurazione['upload_multiplo_script_obbligatorio'] .= " 
								if(uploadCompletato > 0) {
									swfu_".$configurazione['upload_multiplo_file'].".startUpload();
								} else {
									document.forms.formEditor.submit(); void(0);
								}";
					?>
					
					<div class="fieldset flash" id="fsUploadProgress_<? echo $nome; ?>">
					</div>
					<div style="display:inline;">
						<span id="spanButtonPlaceHolder_<? echo $nome; ?>"></span>
					</div>
					<div id="divStatus_<? echo $nome; ?>" style="display:inline;" class="divstatus">0 File inviati</div>
					<input class="stileForm" type="hidden" name="PHPUPSESSID_<? echo $nome; ?>" size="28" value="<? echo $mktime.$configurazione['upload_multiplo_file']; ?>">
					<input type="hidden" id="<? echo $nome; ?>azione" name="<? echo $nome; ?>azione" value="nessuna" />
					<?
					 
					
				} else {
					
					// in modifica metodo classico
					if ($classeStr == '') {
						$styleStr = "style=\"width:100px !important;margin:0px 6px 0px 0px;display:none;\"";
					} else {
						$styleStr = "";
					}
					// il file è in modifica, inserisco anche il bottone di eliminazione del file
                    echo "<span style=\"padding:0px 10px 0px 0px;\">".$valoreVero."</span>";
					echo "<input ".$classeStr." ".$evento." type=\"file\" id=\"".$nome."\" name=\"".$nome."\" value=\"\" ".$styleStr." style=\"display:none;\"  />";
					echo "<input type=\"input\" disabled=\"true\" id=\"".$nome."nome\" name=\"".$nome."nome\" value=\"File da eliminare\" style=\"display:none;width:84px;margin:0px 10px 0px 0px;\" />";
					echo "<input type=\"hidden\" id=\"".$nome."azione\" name=\"".$nome."azione\" value=\"nessuna\" />";
					// inserisco il codice javascript necessario
					echo "<script type=\"text/javascript\">
					function cancellaFile".$nome."() {
						// richiesta di cancellazione per il file
						filecampo = document.getElementById('".$nome."');
						filecampo.style.display = 'none';
						filenome = document.getElementById('".$nome."nome');
						filenome.style.display = 'inline';
						document.getElementById('".$nome."azione').value = 'elimina';
					}
					function nonCancellaFile".$nome."() {
						// richiesta di cancellazione per il file
						filecampo = document.getElementById('".$nome."');
						filecampo.style.display = 'block';
						filenome = document.getElementById('".$nome."nome');
						filenome.style.display = 'none';
						document.getElementById('".$nome."azione').value = 'modifica';
					}
					</script>";
					echo "
					        <a class=\"bottoneClassico\" id=\"ancora".$nome."\" title=\"Elimina il file\" href=\"javascript:cancellaFile".$nome."();\">
						<img src=\"grafica/admin_skin/classic/file_cancel.gif\" alt=\"Elimina il file\" />Cancella</a>
					        <a class=\"bottoneClassico\" id=\"ancora".$nome."nome\" title=\"Modifica il file\" href=\"javascript:nonCancellaFile".$nome."();\">
						<img src=\"grafica/admin_skin/classic/bozza_piccola.gif\" alt=\"Modifica il file\" />Sostituisci</a>
					";	
				}			
			}
?>
