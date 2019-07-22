jQuery.noConflict();

jQuery(document).ready(function() {

	// effetto sottomenu nel menu a sinistra
	jQuery('.leftmenu .dropdown > a').click(function() {
		if (!jQuery(this).next().is(':visible'))
			jQuery(this).next().slideDown('fast');
		else
			jQuery(this).next().slideUp('fast');
		return false;
	});

	/*
	if (jQuery.uniform)
		jQuery('input:checkbox, input:radio, .uniform-file').uniform();
	*/

	if (jQuery('.widgettitle .close').length > 0) {
		jQuery('.widgettitle .close').click(function() {
			jQuery(this).parents('.widgetbox').fadeOut(function() {
				jQuery(this).remove();
			});
		});
	}

	// Aggiungi barra menu per telefonini e tablet
	jQuery('<div class="topbar"><a class="barmenu">' + '</a></div>')
			.insertBefore('.mainwrapper');

	jQuery('.topbar .barmenu').click(function() {

		var lwidth = '260px';
		if (jQuery(window).width() < 340) {
			lwidth = '240px';
		}

		if (!jQuery(this).hasClass('open')) {
			jQuery('.rightpanel, .headerinner, .topbar').css({
				marginLeft : lwidth
			}, 'fast');
			jQuery('.logo, .leftpanel').css({
				marginLeft : 0
			}, 'fast');
			jQuery(this).addClass('open');
		} else {
			jQuery('.rightpanel, .headerinner, .topbar').css({
				marginLeft : 0
			}, 'fast');
			jQuery('.logo, .leftpanel').css({
				marginLeft : '-' + lwidth
			}, 'fast');
			jQuery(this).removeClass('open');
		}
	});

	// mostra/nascondi menu di sinistra
	jQuery(window).resize(function() {
		if(!jQuery('#box-template').val()) {
			if (!jQuery('.topbar').is(':visible')) {
				jQuery('.rightpanel, .headerinner').css({
					marginLeft : '260px'
				});
				jQuery('.logo, .leftpanel').css({
					marginLeft : 0
				});
			} else {
				jQuery('.rightpanel, .headerinner').css({
					marginLeft : 0
				});
				jQuery('.logo, .leftpanel').css({
					marginLeft : '-260px'
				});
			}
		}
	});

	// menu a discesa per la foto profilo
	jQuery('.userloggedinfo img').click(function() {
		if (jQuery(window).width() < 480) {
			var dm = jQuery('.userloggedinfo .userinfo');
			if (dm.is(':visible')) {
				dm.hide();
			} else {
				dm.show();
			}
		}
	});

	jQuery('.show_modalIFrame').on('click', function(e) {
		e.preventDefault();
		var url = jQuery(this).attr("data-href");
		var title = jQuery(this).attr("data-original-title");
		jQuery("#modaleIFrameReview iframe").attr("src", url);
		jQuery("#modaleIFrameReview").modal("show");
		jQuery("#modaleIFrameReview #modaleLabeliFrame").html(title);
	});
	
	jQuery('.btn-errori-element').on('click', function(e) {
		e.preventDefault();
		console.log(this);
		jQuery("#modaleErroriReview .modal-body").html(jQuery('.'+jQuery(this).attr("data-content")).html());
		jQuery("#modaleErroriReview").modal("show");
	});

});

// Get the HTTP Object
function getHTTPObject() {
	if (window.ActiveXObject)
		return new ActiveXObject("Microsoft.XMLHTTP");
	else if (window.XMLHttpRequest)
		return new XMLHttpRequest();
	else {
		alert("Il tuo browser non supporta AJAX: non &egrave; possibile quindi utilizzare la feature.");
		return null;
	}
}

var httpObject = getHTTPObject();

function setOggettiWebservice() {
	if (httpObject.readyState == 4) {
		document.getElementById('div_oggetto_ws').innerHTML = httpObject.responseText;
	}
}

function getOggettiWebservice() {
	if (httpObject != null) {
		var call = "./ajax.php?azione=getOggettiWebservice&idServer="
				+ document.getElementById('server_ws').selectedIndex;
		httpObject.open("GET", call, true);
		httpObject.onreadystatechange = setOggettiWebservice;
		httpObject.send(null);
	}

}

function showAttendere() {
	var fo = new FlashObject("grafica/admin/loading.swf", "fotester", "32",
			"32", "8", "#FFFFFF");
	fo.write("contentDivAttendere");
	document.getElementById("divAttendere").style.display = "block";
}
function ripristinaBackup(redirect) {
	if (confirm("ATTENZIONE: I dati presenti attualmente verranno ripristinati con quelli del backup selezionato. Procedere?")) {
		showAttendere();
		location = redirect;
	} else {
		return;
	}
}

function refreshPagina() {
	// opener.location.reload();
	opener.location = 'index.php?id_sezione=<? echo $idSezione; ?>';
}
function MM_reloadPage(init) { // reloads the window if Nav4 resized
	if (init == true)
		with (navigator) {
			if ((appName == "Netscape") && (parseInt(appVersion) == 4)) {
				document.MM_pgW = innerWidth;
				document.MM_pgH = innerHeight;
				onresize = MM_reloadPage;
			}
		}
	else if (innerWidth != document.MM_pgW || innerHeight != document.MM_pgH)
		location.reload();
}
MM_reloadPage(true);
function apriFinestraHelp(pagina) {
	// strAttr = "status:no;dialogWidth:540px;dialogHeight:420px;scroll:1";' '
	// help = showModalDialog(pagina, null, strAttr);
	// window.open(pagina,'','')";
}
function visualizzAdmin(oggetto, out) {
	// alert('devo operare su admin: sono '+out);
	// alert('Apro amministrazione: '+oggetto);
	if (document.getElementById(oggetto)) {
		var visualizzata = document.getElementById(oggetto).style.display;
		if (visualizzata == "block" || out != "in") {
			document.getElementById(oggetto).style.display = "none";
		} else {
			document.getElementById(oggetto).style.display = "block";
			document.getElementById(oggetto).style.zIndex = 9999;
			// devo aumentare anche il livello dell'elemento che lo contiete

			// alert('Il livello di questo pannello �:
			// '+document.getElementById(oggetto).style.zIndex);
		}

	}
}
// il controllo degli eventi js viene sempre inserito anche se non utilizzato
// nelle funzioni normali accessibili
function addEvent(obj, type, fn) {
	// alert ('aggiungo evento su: '+fn);
	if (obj.attachEvent) {
		obj['e' + type + fn] = fn;
		obj[type + fn] = function() {
			obj['e' + type + fn](window.event);
		}
		obj.attachEvent('on' + type, obj[type + fn]);
	} else {
		obj.addEventListener(type, fn, false);
	}
}
function removeEvent(obj, type, fn) {
	if (obj.detachEvent) {
		obj.detachEvent('on' + type, obj[type + fn]);
		obj[type + fn] = null;
	} else {
		obj.removeEventListener(type, fn, false);
	}
}

function lista_push(lista, testo, valore) {
	// inserisco una nuova voce in un select
	lista.options[lista.options.length] = new Option(testo, valore);
}

function esportaODT(id_ogg, id_doc) {
	var w = window
			.open('export_odt.php?id_ogg=' + id_ogg + '&id_doc=' + id_doc);
}

function archiviaInformazione(id_ogg, id_doc) {
	jQuery
			.ajax({
				type : 'GET',
				url : 'ajax.php?azione=archiviaInformazione&id_ogg=' + id_ogg
						+ '&id_doc=' + id_doc,
				dataType : 'json',
				success : function(response) {
					if (response.esito == 'ko') {
						testo = 'Si e\' verificato un errore. Contattare l\'amministratore del sito.';
						jQuery('<div />').html(testo).dialog({
							title : 'ERRORE',
							modal : true,
							resizable : false,
							draggable : false,
							width : '600',
							close : function() {
								jQuery(this).dialog('destroy').remove();
							},
							buttons : [ {
								text : "Chiudi",
								class : 'btn btn-primary',
								click : function() {
									jQuery(this).dialog("close");
								}
							} ]
						});
					} else {
						jQuery('<div />')
								.html(response.testo)
								.dialog(
										{
											title : 'Archiviazione elemento',
											modal : true,
											resizable : false,
											draggable : false,
											width : '600',
											close : function() {
												jQuery(this).dialog('destroy')
														.remove();
											},
											buttons : [
													{
														text : "Annulla",
														class : 'btn btn-primary',
														click : function() {
															jQuery(this)
																	.dialog(
																			"close");
														}
													},
													{
														text : "Procedi",
														class : 'btn btn-primary',
														click : function() {
															if (jQuery(
																	"#__archiviazione_data_fine")
																	.val() == '') {
																jQuery(
																		'#__data_archiviazione_obbligo')
																		.css(
																				'display',
																				'block');
																return false;
															}
															par = '';
															if (id_ogg == 13) {
																par += '&__archiviazione_data_attivo_al='
																		+ jQuery(
																				"#__archiviazione_data_attivo_al")
																				.val();
															} else if (id_ogg == 3) {
																par += '&carica_inizio='
																		+ jQuery(
																				"#carica_inizio")
																				.val()
																		+ '&carica_fine='
																		+ jQuery(
																				"#carica_fine")
																				.val();
															} else if (id_ogg == 43) {
																par += '&data_attivazione='
																		+ jQuery(
																				"#data_attivazione")
																				.val()
																		+ '&data_scadenza='
																		+ jQuery(
																				"#data_scadenza")
																				.val();
															}
															df = jQuery(
																	"#__archiviazione_data_fine")
																	.val();
															jQuery(this)
																	.dialog(
																			"close");
															jQuery
																	.ajax({
																		type : 'GET',
																		url : 'ajax.php?azione=confermaArchiviaInformazione&id_ogg='
																				+ id_ogg
																				+ '&id_doc='
																				+ id_doc
																				+ '&data_fine='
																				+ df
																				+ par,
																		dataType : 'json',
																		success : function(
																				response) {
																			if (response.esito == 'ko') {
																				testo = 'Si e\' verificato un errore. Contattare l\'amministratore del sito.';
																				jQuery(
																						'<div />')
																						.html(
																								testo)
																						.dialog(
																								{
																									title : 'ERRORE',
																									modal : true,
																									resizable : false,
																									draggable : false,
																									width : '600',
																									close : function() {
																										jQuery(
																												this)
																												.dialog(
																														'destroy')
																												.remove();
																									},
																									buttons : [ {
																										text : "Chiudi",
																										class : 'btn btn-primary',
																										click : function() {
																											jQuery(
																													this)
																													.dialog(
																															"close");
																										}
																									} ]
																								});
																			} else {
																				jQuery(
																						'<div />')
																						.html(
																								response.testo)
																						.dialog(
																								{
																									title : 'Archiviazione elemento completata',
																									modal : true,
																									resizable : false,
																									draggable : false,
																									width : '600',
																									close : function() {
																										jQuery(
																												this)
																												.dialog(
																														'destroy')
																												.remove();
																									},
																									buttons : [ {
																										text : "Chiudi",
																										class : 'btn btn-primary',
																										click : function() {
																											jQuery(
																													'#btn__archiviata_'
																															+ id_ogg
																															+ '_'
																															+ id_doc)
																													.css(
																															'display',
																															'none');
																											jQuery(
																													'#btn__duplicata_'
																															+ id_ogg
																															+ '_'
																															+ id_doc)
																													.css(
																															'display',
																															'none');
																											jQuery(
																													'#btn__versioning_'
																															+ id_ogg
																															+ '_'
																															+ id_doc)
																													.css(
																															'display',
																															'none');
																											jQuery(
																													'#td1_row_'
																															+ id_ogg
																															+ '_'
																															+ id_doc)
																													.append(
																															"&nbsp;<span class=\"intTooltip\"><a href=\"#\" data-placement=\"top\" data-rel=\"tooltip\" data-original-title=\"Questo elemento &egrave; stato archiviato.\" class=\"btn\"><span class=\"iconfa-folder-close\" style=\"color: #FF5511;\"></span></a></span>");
																											// RIPETO
																											// INIZIALIZZAZIONE
																											// DEI
																											// TOOLTIP
																											if (jQuery('table .intTooltip').length > 0) {
																												jQuery(
																														'table a[data-rel]')
																														.each(
																																function() {
																																	jQuery(
																																			this)
																																			.attr(
																																					'rel',
																																					jQuery(
																																							this)
																																							.data(
																																									'rel'));
																																});
																												jQuery(
																														'table .intTooltip')
																														.tooltip(
																																{
																																	selector : "a[rel=tooltip]"
																																});
																											}
																											jQuery(
																													this)
																													.dialog(
																															"close");
																										}
																									} ]
																								});
																			}
																		},
																		error : function() {
																			alert('Si e\' verificato un errore riguardo la tua connessione');
																		}
																	});
														}
													} ]
										});

						if (id_ogg == 13) {
							// archiviazione uffici
							jQuery('#__data_archiviazione')
									.append(
											"<div>Attivo fino al</div><div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-calendar\"></span></span></div>"
													+ "<input type=\"text\" name=\"__archiviazione_data_attivo_alVis\" id=\"__archiviazione_data_attivo_alVis\" class=\"input-small\" />"
													+ "<input type=\"hidden\" name=\"__archiviazione_data_attivo_al\" id=\"__archiviazione_data_attivo_al\" />");
							jQuery('#__archiviazione_data_attivo_alVis')
									.datepicker({
										changeMonth : true,
										changeYear : true
									});
							jQuery("#__archiviazione_data_attivo_alVis")
									.change(
											function() {
												if (jQuery(
														"#__archiviazione_data_attivo_alVis")
														.val() == '') {
													jQuery(
															"#__archiviazione_data_attivo_al")
															.val('');
												} else {
													jQuery(
															"#__archiviazione_data_attivo_al")
															.val(
																	jQuery(
																			"#__archiviazione_data_attivo_alVis")
																			.val());
												}
											});
						} else if (id_ogg == 3) {
							// personale
							jQuery('#__data_archiviazione')
									.append(
											"<div>In carica dal</div><div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-calendar\"></span></span></div>"
													+ "<input type=\"text\" name=\"carica_inizioVis\" id=\"carica_inizioVis\" class=\"input-small\" value=\""
													+ response.carica_inizio
													+ "\" />"
													+ "<input type=\"hidden\" name=\"carica_inizio\" id=\"carica_inizio\" value=\""
													+ response.carica_inizio
													+ "\" />");
							jQuery('#carica_inizioVis').datepicker({
								changeMonth : true,
								changeYear : true
							});
							jQuery("#carica_inizioVis")
									.change(
											function() {
												if (jQuery("#carica_inizioVis")
														.val() == '') {
													jQuery("#carica_inizio")
															.val('');
												} else {
													jQuery("#carica_inizio")
															.val(
																	jQuery(
																			"#carica_inizioVis")
																			.val());
												}
											});
							jQuery('#__data_archiviazione')
									.append(
											"<div>In carica fino al</div><div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-calendar\"></span></span></div>"
													+ "<input type=\"text\" name=\"carica_fineVis\" id=\"carica_fineVis\" class=\"input-small\" value=\""
													+ response.carica_fine
													+ "\" />"
													+ "<input type=\"hidden\" name=\"carica_fine\" id=\"carica_fine\" value=\""
													+ response.carica_fine
													+ "\" />");
							jQuery('#carica_fineVis').datepicker({
								changeMonth : true,
								changeYear : true
							});
							jQuery("#carica_fineVis")
									.change(
											function() {
												if (jQuery("#carica_fineVis")
														.val() == '') {
													jQuery("#carica_fine").val(
															'');
												} else {
													jQuery("#carica_fine")
															.val(
																	jQuery(
																			"#carica_fineVis")
																			.val());
												}
											});
						} else if (id_ogg == 43) {
							// commissioni
							jQuery('#__data_archiviazione')
									.append(
											"<div>Attiva dal</div><div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-calendar\"></span></span></div>"
													+ "<input type=\"text\" name=\"data_attivazioneVis\" id=\"data_attivazioneVis\" class=\"input-small\" value=\""
													+ response.data_attivazione
													+ "\" />"
													+ "<input type=\"hidden\" name=\"data_attivazione\" id=\"data_attivazione\" value=\""
													+ response.data_attivazione
													+ "\" />");
							jQuery('#data_attivazioneVis').datepicker({
								changeMonth : true,
								changeYear : true
							});
							jQuery("#data_attivazioneVis")
									.change(
											function() {
												if (jQuery(
														"#data_attivazioneVis")
														.val() == '') {
													jQuery("#data_attivazione")
															.val('');
												} else {
													jQuery("#data_attivazione")
															.val(
																	jQuery(
																			"#data_attivazioneVis")
																			.val());
												}
											});
							jQuery('#__data_archiviazione')
									.append(
											"<div>Attiva fino al</div><div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-calendar\"></span></span></div>"
													+ "<input type=\"text\" name=\"data_scadenzaVis\" id=\"data_scadenzaVis\" class=\"input-small\" value=\""
													+ response.data_scadenza
													+ "\" />"
													+ "<input type=\"hidden\" name=\"data_scadenza\" id=\"data_scadenza\" value=\""
													+ response.data_scadenza
													+ "\" />");
							jQuery('#data_scadenzaVis').datepicker({
								changeMonth : true,
								changeYear : true
							});
							jQuery("#data_scadenzaVis")
									.change(
											function() {
												if (jQuery("#data_scadenzaVis")
														.val() == '') {
													jQuery("#data_scadenza")
															.val('');
												} else {
													jQuery("#data_scadenza")
															.val(
																	jQuery(
																			"#data_scadenzaVis")
																			.val());
												}
											});
						}

						jQuery('#__data_archiviazione')
								.append(
										"<div><span class=\"icon-ok-circle\"></span> Data di fine pubblicazione in archivio</div><div class=\"input-prepend\"><span class=\"add-on\"><span class=\"iconfa-calendar\"></span></span></div>"
												+ "<input type=\"text\" name=\"__archiviazione_data_fineVis\" id=\"__archiviazione_data_fineVis\" class=\"input-small\" />"
												+ "<input type=\"hidden\" name=\"__archiviazione_data_fine\" id=\"__archiviazione_data_fine\" />"
												+ "<div id=\"__data_archiviazione_obbligo\" style=\"display:none;\"><strong>Campo obbligatorio</strong></div>");
						jQuery('#__archiviazione_data_fineVis').datepicker({
							changeMonth : true,
							changeYear : true
						});
						jQuery("#__archiviazione_data_fineVis")
								.change(
										function() {
											if (jQuery(
													"#__archiviazione_data_fineVis")
													.val() == '') {
												jQuery(
														"#__archiviazione_data_fine")
														.val('');
											} else {
												jQuery(
														"#__archiviazione_data_fine")
														.val(
																jQuery(
																		"#__archiviazione_data_fineVis")
																		.val());
											}
										});

					}
				},
				error : function() {
					alert('Si e\' verificato un errore riguardo la tua connessione');
				}
			});
}
function rimuoviArchiviazione() {
	jQuery('#__archiviata_ripubblica').val('1');
	jQuery('#remArchivio').html("<strong>L'elemento ver&agrave; rimosso dall'archivio.</strong> Salvare i dati per completare l'operazione.");
	jQuery('.btnSalvataggio').css('display', 'inline-block');
	jQuery('.jtable-toolbar-item-add-record').show();
	jQuery('.jtable-command-column').show();
	jQuery('.jtable-command-column-header').show();
}

function duplicaInformazione(id_ogg, id_doc) {
	jQuery('<div />')
			.html(
					"L'elemento selezionato verr&agrave; duplicato.<br /><br />"
							+ "E' possibile effettuare una duplicazione: <br />"
							+ " - <strong>Parziale</strong>: duplicazione con esclusione di eventuali file allegati;<br />"
							+ " - <strong>Completa</strong>: duplicazione comprensiva di eventuali file allegati.<br />")
			.dialog({
				title : 'Duplicazione elemento',
				modal : true,
				resizable : false,
				draggable : false,
				width : '600',
				close : function() {
					jQuery(this).dialog('destroy').remove();
				},
				buttons : [ {
					text : "Annulla",
					class : 'btn btn-primary',
					click : function() {
						jQuery(this).dialog("close");
					}
				}, {
					text : "Duplicazione parziale",
					class : 'btn btn-primary',
					click : function() {
						jQuery('#id_duplicazione').val(id_doc);
						jQuery('#formDuplica').submit();
					}
				}, {
					text : "Duplicazione completa",
					class : 'btn btn-primary',
					click : function() {
						jQuery('#id_duplicazione').val(id_doc);
						jQuery('#duplicazione_includi_file').val('1');
						jQuery('#formDuplica').submit();
					}
				} ]
			});
}

function bando2provvedimento(id_doc) {
	jQuery('<div />')
			.html(
					"<div>Creazione di un nuovo record in <strong>Provvedimenti Amministrativi</strong>: verrano copiati i seguenti dati dalla procedura selezionata:</div>"
							+ "<ul style=\"margin-left:30px;\">"
							+ " <li><strong>Oggetto</strong></li>"
							+ " <li><strong>Ufficio</strong></li>"
							+ " <li><strong>Allegati</strong></li>"
							+ "</ul>"
							+ "<div><br />Procedere con la copia?<br /></div>")
			.dialog({
				title : 'Copia in provvedimenti amministrativi',
				modal : true,
				resizable : false,
				draggable : false,
				width : '600',
				close : function() {
					jQuery(this).dialog('destroy').remove();
				},
				buttons : [ {
					text : "Annulla",
					class : 'btn btn-primary',
					click : function() {
						jQuery(this).dialog("close");
					}
				}, {
					text : "Copia",
					class : 'btn btn-primary',
					click : function() {
						jQuery('#id_copia').val(id_doc);
						jQuery('#formBando2Provvedimento').submit();
					}
				} ]
			});
}