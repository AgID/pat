<?

/*
 * Creato il 13/set/2010 da Nico
 */
?>

// Get the HTTP Object
function getHTTPObject(){
    if (window.ActiveXObject)
        return new ActiveXObject("Microsoft.XMLHTTP");
    else if (window.XMLHttpRequest)
        return new XMLHttpRequest();
    else {
        alert("Il tuo browser non supporta AJAX: non è possibile quindi utilizzare la feature.");
        return null;
    }
}

var httpObject = getHTTPObject();

var ultimoPuntoCreato<? echo $nome; ?> = <? echo $numPunti; ?>;

var map<? echo $nome; ?>,geocoder<? echo $nome; ?>,marker<? echo $nome; ?>,bubble<? echo $nome; ?>,overlay<? echo $nome; ?>,latLngControl<? echo $nome; ?>;
var lat<? echo $nome; ?>,lng<? echo $nome; ?>, latlng<? echo $nome; ?>;
var markersArray<? echo $nome; ?> = new Array();

function initializeMap<? echo $nome; ?>() {
  	
	latlng<? echo $nome; ?> = new google.maps.LatLng(lat<? echo $nome; ?>, lng<? echo $nome; ?>);
  	
	var myOptions = {
		zoom: <? echo $zoommappa; ?>,
		center: latlng<? echo $nome; ?>,
		mapTypeId: google.maps.MapTypeId.<? echo $tipomappa; ?>
	};

	map<? echo $nome; ?> = new google.maps.Map(document.getElementById("map_canvas<? echo $nome; ?>"), myOptions);

	// Create new control to display latlng and coordinates under mouse.
	latLngControl<? echo $nome; ?> = new LatLngControl(map<? echo $nome; ?>);
	<?
	if (!isset($contentUrp) or $contentUrp=='') {
		$contentUrp=$indirizzo;
	} 
	?>
	bubble<? echo $nome; ?> = new google.maps.InfoWindow({
		content: "<? echo addslashes($contentUrp) ?>",
		maxWidth: 340
	});
	
			    
	marker<? echo $nome; ?> = new google.maps.Marker({
	    position: latlng<? echo $nome; ?>, 
	    map: map<? echo $nome; ?>,
	    draggable: true,
	    title:"<? echo addslashes($titolomarker) ?>"
	    <? echo ($icona != '' ? ", icon: 'moduli/output_immagine.php?id=".$icona."'" : $icona); ?>
	});
	markersArray<? echo $nome; ?>['marker1'] = marker<? echo $nome; ?>;

	<?
	if ($contentUrp!=$indirizzo) {
		echo "bubble".$nome.".open(map".$nome.",markersArray".$nome."['marker1']);";
	} 
	?>	

	visible<? echo $nome; ?> = false;

	google.maps.event.addListener(map<? echo $nome; ?>, 'mousemove', function(mEvent) {
		latlng<? echo $nome; ?> = mEvent.latLng;
		if(!visible<? echo $nome; ?>)
	  		latLngControl<? echo $nome; ?>.updatePosition(latlng<? echo $nome; ?>);
	});
				    
	google.maps.event.addListener(map<? echo $nome; ?>, 'zoom_changed', function() {
	    document.getElementById('prop_zoommappa<? echo $nome; ?>').value = map<? echo $nome; ?>.getZoom();
	    if(visible<? echo $nome; ?>) {
	    	latLngControl<? echo $nome; ?>.set('visible', false);
	    	visible<? echo $nome; ?> = false;
		}	
		if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
	    propGMaps<? echo $nome; ?>();
	});

	google.maps.event.addListener(map<? echo $nome; ?>, 'click', function() {
	    if(visible<? echo $nome; ?>) {
	    	latLngControl<? echo $nome; ?>.set('visible', false);
	    	visible<? echo $nome; ?> = false;
		}	
		if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
	});

	google.maps.event.addListener(map<? echo $nome; ?>, 'rightclick', function(mEvent) {
		latLngControl<? echo $nome; ?>.updatePosition(latlng<? echo $nome; ?>);
	    if(visible<? echo $nome; ?>) {
			latLngControl<? echo $nome; ?>.set('visible', false);
			latLngControl<? echo $nome; ?>.set('visible', true);
			visible<? echo $nome; ?> = true;
		} else {
			latLngControl<? echo $nome; ?>.set('visible', true);
			visible<? echo $nome; ?> = true;
		}
		if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
	});

	google.maps.event.addListener(marker<? echo $nome; ?>, 'click', function(mEvent) {
		bubble<? echo $nome; ?>.close();
		
		//prendere i dati dall'editor
		htmlEditor<? echo $nome; ?> = '';
		if (CKEDITOR.instances.prop_editor_1_<? echo $nome; ?>) {
			htmlEditor<? echo $nome; ?> = CKEDITOR.instances.prop_editor_1_<? echo $nome; ?>.getData();
		}
		bubble<? echo $nome; ?> = new google.maps.InfoWindow({
	        content: htmlEditor<? echo $nome; ?>,
	        maxWidth: 340
	    });
		bubble<? echo $nome; ?>.open(map<? echo $nome; ?>,markersArray<? echo $nome; ?>['marker1']);
		if(visible<? echo $nome; ?>) {
	    	latLngControl<? echo $nome; ?>.set('visible', false);
	    	visible<? echo $nome; ?> = false;
		}	
		if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
			
		puntoSelezionato<? echo $nome; ?> = 1;
	});

	google.maps.event.addListener(marker<? echo $nome; ?>, 'rightclick', function(mEvent) {
		if(visible<? echo $nome; ?>) {
			latLngControl<? echo $nome; ?>.updatePosition(latlng<? echo $nome; ?>);
		}
		latLngControl<? echo $nome; ?>.set('visible', true);
		visible<? echo $nome; ?> = true;
		
		if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'block';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'none';
		
		puntoSelezionato<? echo $nome; ?> = 1;
	});

	google.maps.event.addListener(marker<? echo $nome; ?>, 'dragend', function(mEvent) {
		if(visible<? echo $nome; ?>) {
	    	latLngControl<? echo $nome; ?>.set('visible', false);
	    	visible<? echo $nome; ?> = false;
		}	
		if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
			
		puntoSelezionato<? echo $nome; ?> = 1;
		setAddressByPos<? echo $nome; ?>('1');
		propGMapsCampo<? echo $nome; ?>('1');
	});
				

	google.maps.event.addListener(map<? echo $nome; ?>, 'maptypeid_changed', function() {
		sel<? echo $nome; ?> = document.getElementById('prop_tipomappa<? echo $nome; ?>');
		tipo<? echo $nome; ?> = map<? echo $nome; ?>.getMapTypeId().toUpperCase();
		for (var i=0; i < sel<? echo $nome; ?>.options.length; i++){
		 	if (sel<? echo $nome; ?>.options[i].value == tipo<? echo $nome; ?>){
		  		sel<? echo $nome; ?>.selectedIndex = i;
		 	}
		}
		if(visible<? echo $nome; ?>) {
			latLngControl<? echo $nome; ?>.set('visible', false);
			visible<? echo $nome; ?> = false;
		}	
		if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
		propGMaps<? echo $nome; ?>();
	});
	
	var noPoi = [{
	    featureType: "poi",
	    elementType: "labels",
	    stylers: [{
	    	visibility: "off"
		}]
	}];

	map<? echo $nome; ?>.setOptions({
	    styles: noPoi
	});
				    
}


function initialize<? echo $nome; ?>() {
  	geocoder<? echo $nome; ?> = new google.maps.Geocoder();
  	
  	<? if($oldAPI) { ?>
  		lat<? echo $nome; ?> = '<? echo $lat; ?>';
        lng<? echo $nome; ?> = '<? echo $lng; ?>';
        initializeMap<? echo $nome; ?>();
        addMarkers<? echo $nome; ?>();  //funzione che inizializza tutti gli altri marker e li visualizza sulla mappa
  	
  	<? } else { ?>
  	
  		lat<? echo $nome; ?> = '<? echo $lat; ?>';
        lng<? echo $nome; ?> = '<? echo $lng; ?>';
	  	indirizzo<? echo $nome; ?> = '<? echo addslashes($indirizzo); ?>';
        
        if(lat<? echo $nome; ?> != '') {
        	initializeMap<? echo $nome; ?>();
        	addMarkers<? echo $nome; ?>();  //funzione che inizializza tutti gli altri marker e li visualizza sulla mappa
        } else { 
		  	geocoder<? echo $nome; ?>.geocode( { 'address': indirizzo<? echo $nome; ?>}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
			    	lat<? echo $nome; ?> = results[0].geometry.location.lat();
			        lng<? echo $nome; ?> = results[0].geometry.location.lng();
			        initializeMap<? echo $nome; ?>();
			        addMarkers<? echo $nome; ?>();  //funzione che inizializza tutti gli altri marker e li visualizza sulla mappa
			    }
		  	});
		}
	  	
	<? } ?>
}
  	
function initializeMarker<? echo $nome; ?>(num, indirizzo, titolo) {
  		
	latlng<? echo $nome; ?> = new google.maps.LatLng(lat<? echo $nome; ?>, lng<? echo $nome; ?>);
	  	
	marker<? echo $nome; ?> = new google.maps.Marker({
	    position: latlng<? echo $nome; ?>, 
		map: map<? echo $nome; ?>,
		draggable: true,
		title: titolo
	});
	markersArray<? echo $nome; ?>['marker'+num] = marker<? echo $nome; ?>;
	
	icon = document.getElementById('prop_icona_'+num+'_<? echo $nome; ?>').value;
	if(icon != 0) {
		src = 'moduli/output_immagine.php?id='+icon;
		markersArray<? echo $nome; ?>['marker'+num].setIcon(src);
	}
		
	google.maps.event.addListener(marker<? echo $nome; ?>, 'click', function() {
	
		//prendere i dati dall editor
		temp = " htmlEditor = ''; if (CKEDITOR.instances.prop_editor_'+num+'_<? echo $nome; ?>) { htmlEditor = CKEDITOR.instances.prop_editor_'+num+'_<? echo $nome; ?>.getData(); } ";
		eval(temp);
	
	    bubble<? echo $nome; ?>.close();
	    bubble<? echo $nome; ?> = new google.maps.InfoWindow({
			content: htmlEditor,
			maxWidth: 340
		});
		bubble<? echo $nome; ?>.open(map<? echo $nome; ?>,markersArray<? echo $nome; ?>['marker'+num]);
		if(visible<? echo $nome; ?>) {
		   	latLngControl<? echo $nome; ?>.set('visible', false);
		   	visible<? echo $nome; ?> = false;
	    }	
	    if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
				
		puntoSelezionato<? echo $nome; ?> = num;
	});
	    
	google.maps.event.addListener(marker<? echo $nome; ?>, 'rightclick', function() {
    	latLngControl<? echo $nome; ?>.updatePosition(latlng<? echo $nome; ?>);
	    latLngControl<? echo $nome; ?>.set('visible', true);
	    visible<? echo $nome; ?> = true;
    		
	    if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'block';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'none';
				
		puntoSelezionato<? echo $nome; ?> = num;
	});
	    
	google.maps.event.addListener(marker<? echo $nome; ?>, 'dragend', function() {
	    if(visible<? echo $nome; ?>) {
		   	latLngControl<? echo $nome; ?>.set('visible', false);
		   	visible<? echo $nome; ?> = false;
	    }	
	    if(document.getElementById('divElimina<? echo $nome; ?>') != null)
			document.getElementById('divElimina<? echo $nome; ?>').style.display = 'none';
		if(document.getElementById('divAggiungi<? echo $nome; ?>') != null)
			document.getElementById('divAggiungi<? echo $nome; ?>').style.display = 'block';
				
		puntoSelezionato<? echo $nome; ?> = num;
		setAddressByPos<? echo $nome; ?>(num);
		propGMapsCampo<? echo $nome; ?>(num);
	});
}
  	
function setMarkerTitle<? echo $nome; ?>(element) {
	index = element.id.split('_');
    index = index[2];
    markersArray<? echo $nome; ?>['marker'+index].setTitle(document.getElementById('prop_titolomarker_'+index+'_<? echo $nome; ?>').value);
  	propGMapsCampo<? echo $nome; ?>(index);
}

function setIcon(index) {
	src = 'moduli/output_immagine.php?id='+document.getElementById('prop_icona_'+index+'_<? echo $nome; ?>').value;
	markersArray<? echo $nome; ?>['marker'+index].setIcon(src);
  	propGMapsCampo<? echo $nome; ?>(index);
}
  	
var num,via,citta,cittadina,regione,stato,cap;

function setAddressEnter<? echo $nome; ?>(element) {
	if(event.keyCode=='13') {
  		index = element.id.split('_');
    	index = index[2];
  		indirizzo<? echo $nome; ?> = document.getElementById('prop_indirizzo_'+index+'_<? echo $nome; ?>').value;
  		if(indirizzo<? echo $nome; ?> != '') { 
	  		geocoder<? echo $nome; ?>.geocode( { 'address': indirizzo<? echo $nome; ?>}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
		      		map<? echo $nome; ?>.setCenter(results[0].geometry.location);
		        	markersArray<? echo $nome; ?>['marker'+index].setPosition(results[0].geometry.location);
					
						 
					
		        	indirizzi = results[0].address_components;
		        	indirizzo<? echo $nome; ?> = ''; num = ''; via = ''; cittadina = ''; citta = ''; regione = ''; stato = ''; cap = '';
		        	for (var i=0; i < indirizzi.length; i++) {
						if(indirizzi[i].types == 'street_number')
							num = indirizzi[i].long_name;
						if(indirizzi[i].types == 'route')
							via = indirizzi[i].long_name;
						if(indirizzi[i].types == 'locality,political')
							cittadina = indirizzi[i].long_name;
						if(indirizzi[i].types == 'administrative_area_level_2,political')
							citta = indirizzi[i].long_name;
						if(indirizzi[i].types == 'administrative_area_level_1,political')
							regione = indirizzi[i].long_name;
						if(indirizzi[i].types == 'country,political')
							stato = indirizzi[i].long_name;
						if(indirizzi[i].types == 'postal_code')
							cap = indirizzi[i].long_name;
					}
					if(via != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += via;
					}
					if(num != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += num;
					}
					if(cittadina != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cittadina;
					}
					if(citta != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += citta;
					}
					if(cap != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cap;
					}
					if(regione != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += regione;
					}
					if(stato != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += stato;
					}
					document.getElementById('prop_indirizzo_'+index+'_<? echo $nome; ?>').value = indirizzo<? echo $nome; ?>;
					document.getElementById('prop_lat_'+index+'_<? echo $nome; ?>').value = results[0].geometry.location.lat();
					document.getElementById('prop_lng_'+index+'_<? echo $nome; ?>').value = results[0].geometry.location.lng();
					bubble<? echo $nome; ?>.setContent(indirizzo<? echo $nome; ?>);
					bubble<? echo $nome; ?>.open(map<? echo $nome; ?>,markersArray<? echo $nome; ?>['marker'+index]);
					propGMapsCampo<? echo $nome; ?>(index);

  				} else {
    				alert("Impossibile trovare l'indirizzo (" + status + ")");
				}
		    });
	    }	
		alert('test');
		return false;
	}
	
	
}
	
function setAddress<? echo $nome; ?>(element) {
  		index = element.id.split('_');
    	index = index[2];
  		indirizzo<? echo $nome; ?> = document.getElementById('prop_indirizzo_'+index+'_<? echo $nome; ?>').value;
  		if(indirizzo<? echo $nome; ?> != '') { 
	  		geocoder<? echo $nome; ?>.geocode( { 'address': indirizzo<? echo $nome; ?>}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
		      		map<? echo $nome; ?>.setCenter(results[0].geometry.location);
		        	markersArray<? echo $nome; ?>['marker'+index].setPosition(results[0].geometry.location);
					
						 
					
		        	indirizzi = results[0].address_components;
		        	indirizzo<? echo $nome; ?> = ''; num = ''; via = ''; cittadina = ''; citta = ''; regione = ''; stato = ''; cap = '';
		        	for (var i=0; i < indirizzi.length; i++) {
						if(indirizzi[i].types == 'street_number')
							num = indirizzi[i].long_name;
						if(indirizzi[i].types == 'route')
							via = indirizzi[i].long_name;
						if(indirizzi[i].types == 'locality,political')
							cittadina = indirizzi[i].long_name;
						if(indirizzi[i].types == 'administrative_area_level_2,political')
							citta = indirizzi[i].long_name;
						if(indirizzi[i].types == 'administrative_area_level_1,political')
							regione = indirizzi[i].long_name;
						if(indirizzi[i].types == 'country,political')
							stato = indirizzi[i].long_name;
						if(indirizzi[i].types == 'postal_code')
							cap = indirizzi[i].long_name;
					}
					if(via != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += via;
					}
					if(num != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += num;
					}
					if(cittadina != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cittadina;
					}
					if(citta != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += citta;
					}
					if(cap != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cap;
					}
					if(regione != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += regione;
					}
					if(stato != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += stato;
					}
					document.getElementById('prop_indirizzo_'+index+'_<? echo $nome; ?>').value = indirizzo<? echo $nome; ?>;
					document.getElementById('prop_lat_'+index+'_<? echo $nome; ?>').value = results[0].geometry.location.lat();
					document.getElementById('prop_lng_'+index+'_<? echo $nome; ?>').value = results[0].geometry.location.lng();
					bubble<? echo $nome; ?>.setContent(indirizzo<? echo $nome; ?>);
					bubble<? echo $nome; ?>.open(map<? echo $nome; ?>,markersArray<? echo $nome; ?>['marker'+index]);
					propGMapsCampo<? echo $nome; ?>(index);

  				} else {
    				alert("Impossibile trovare l'indirizzo (" + status + ")");
				}
		    });
	    }		  		
}

function setAddressForced<? echo $nome; ?>(element) {
  		index = element.id.split('_');
    		index = index[2];
  		indirizzo<? echo $nome; ?> = document.getElementById('prop_indirizzo_'+index+'_<? echo $nome; ?>').value;
  		if(indirizzo<? echo $nome; ?> != '') {
	  		geocoder<? echo $nome; ?>.geocode( { 'address': indirizzo<? echo $nome; ?>}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
		      		map<? echo $nome; ?>.setCenter(results[0].geometry.location);
					
		        	markersArray<? echo $nome; ?>['marker'+index].setPosition(results[0].geometry.location);
		        
		        	indirizzi = results[0].address_components;
		        	indirizzo<? echo $nome; ?> = ''; num = ''; via = ''; cittadina = ''; citta = ''; regione = ''; stato = ''; cap = '';
		        	for (var i=0; i < indirizzi.length; i++) {
						if(indirizzi[i].types == 'street_number')
							num = indirizzi[i].long_name;
						if(indirizzi[i].types == 'route')
							via = indirizzi[i].long_name;
						if(indirizzi[i].types == 'locality,political')
							cittadina = indirizzi[i].long_name;
						if(indirizzi[i].types == 'administrative_area_level_2,political')
							citta = indirizzi[i].long_name;
						if(indirizzi[i].types == 'administrative_area_level_1,political')
							regione = indirizzi[i].long_name;
						if(indirizzi[i].types == 'country,political')
							stato = indirizzi[i].long_name;
						if(indirizzi[i].types == 'postal_code')
							cap = indirizzi[i].long_name;
					}
					if(via != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += via;
					}
					if(num != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += num;
					}
					if(cittadina != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cittadina;
					}
					if(citta != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += citta;
					}
					if(cap != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cap;
					}
					if(regione != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += regione;
					}
					if(stato != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += stato;
					}
					document.getElementById('prop_indirizzo_'+index+'_<? echo $nome; ?>').value = indirizzo<? echo $nome; ?>;
					bubble<? echo $nome; ?>.setContent(indirizzo<? echo $nome; ?>);
					bubble<? echo $nome; ?>.open(map<? echo $nome; ?>,markersArray<? echo $nome; ?>['marker'+index]);
					propGMapsCampo<? echo $nome; ?>(index);
  				} else {
    				alert("Impossibile trovare l'indirizzo (" + status + ")");
				}
		    });
	    }		  		
}


 
function setAddressByPos<? echo $nome; ?>(index) {
	latlng<? echo $nome; ?> = markersArray<? echo $nome; ?>['marker'+index].getPosition();
	if(latlng<? echo $nome; ?> != '') { 
		geocoder<? echo $nome; ?>.geocode( { 'location': latlng<? echo $nome; ?>}, function(results, status) {
  			if (status == google.maps.GeocoderStatus.OK) {
			    map<? echo $nome; ?>.setCenter(results[0].geometry.location);
			    indirizzi = results[0].address_components;
			    indirizzo<? echo $nome; ?> = ''; num = ''; via = ''; cittadina = ''; citta = ''; regione = ''; stato = ''; cap = '';
				for (var i=0; i < indirizzi.length; i++) {
					if(indirizzi[i].types == 'street_number')
						num = indirizzi[i].long_name;
					if(indirizzi[i].types == 'route')
						via = indirizzi[i].long_name;
					if(indirizzi[i].types == 'locality,political')
						cittadina = indirizzi[i].long_name;
					if(indirizzi[i].types == 'administrative_area_level_2,political')
						citta = indirizzi[i].long_name;
					if(indirizzi[i].types == 'administrative_area_level_1,political')
						regione = indirizzi[i].long_name;
					if(indirizzi[i].types == 'country,political')
						stato = indirizzi[i].long_name;
					if(indirizzi[i].types == 'postal_code')
						cap = indirizzi[i].long_name;
				}
				if(via != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += via;
					}
					if(num != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += num;
					}
					if(cittadina != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cittadina;
					}
					if(citta != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += citta;
					}
					if(cap != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += cap;
					}
					if(regione != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += regione;
					}
					if(stato != '') {
						if(indirizzo<? echo $nome; ?> != '')
							indirizzo<? echo $nome; ?> += ', ';
						indirizzo<? echo $nome; ?> += stato;
					}
				document.getElementById('prop_indirizzo_'+index+'_<? echo $nome; ?>').value = indirizzo<? echo $nome; ?>;
				document.getElementById('prop_lat_'+index+'_<? echo $nome; ?>').value = results[0].geometry.location.lat();
				document.getElementById('prop_lng_'+index+'_<? echo $nome; ?>').value = results[0].geometry.location.lng();
				bubble<? echo $nome; ?>.setContent(indirizzo<? echo $nome; ?>);
				bubble<? echo $nome; ?>.open(map<? echo $nome; ?>,markersArray<? echo $nome; ?>['marker'+index]);
			    propGMapsCampo<? echo $nome; ?>(index);
	  		} else {
	    		alert("Impossibile trovare l'indirizzo (" + status + ")");
			}
		});
	}		  		
}
  
function setMapType<? echo $nome; ?>() {
	tipo = document.getElementById('prop_tipomappa<? echo $nome; ?>');
	tipo = tipo[tipo.selectedIndex].value;
	switch(tipo) {
		case 'ROADMAP':
			tipo = google.maps.MapTypeId.ROADMAP;
		break;
		case 'SATELLITE':
			tipo = google.maps.MapTypeId.SATELLITE;
		break;
		case 'HYBRID':
			tipo = google.maps.MapTypeId.HYBRID;
		break;
		case 'TERRAIN':
		  	tipo = google.maps.MapTypeId.TERRAIN;
		break;
	}
  	map<? echo $nome; ?>.setMapTypeId(tipo);
  	propGMaps<? echo $nome; ?>();
}

function setBubbleEditor<? echo $nome; ?>(numMarkers){
    if(httpObject.readyState == 4 ){
        document.getElementById('spanEditor_'+numMarkers+'_<? echo $nome; ?>').innerHTML = httpObject.responseText;
        div = 'divprop_editor_'+numMarkers+'_<? echo $nome; ?>';
		tempFun = document.getElementById(div).innerHTML;
		eval(tempFun);
    }
}

function getBubbleEditor<? echo $nome; ?>(numMarkers){
    if (httpObject != null) {
    	var call = "./ajax.php?azione=getBubbleEditorObject&numMarkers="+numMarkers+"&nome=<? echo $nome; ?>";
        httpObject.open("GET", call, true);
        httpObject.onreadystatechange = function() {setBubbleEditor<? echo $nome; ?>(numMarkers)};
        httpObject.send(null);
    }
}

function deleteCache(id,nomecampo) {
	if (httpObject != null) {
    	var call = "./ajax.php?azione=deleteMapsCache&id="+id+"&nomecampo="+nomecampo;
        httpObject.open("GET", call, true);
        httpObject.onreadystatechange = function () {};
        httpObject.send(null);
    }
}


/**
 * LatLngControl class displays the LatLng and pixel coordinates
 * underneath the mouse within a container anchored to it.
 * @param {google.maps.Map} map Map to add custom control to.
 */
function LatLngControl(map) {
	/**
     * Offset the control container from the mouse by this amount.
     */
    this.ANCHOR_OFFSET_ = new google.maps.Point(8, 8);
    
    maxX = 400;
    maxY = 400;
    
    /**
     * Pointer to the HTML container.
     */
    this.node_ = this.createHtmlNode_();
    
    // Add control to the map. Position is irrelevant.
    map.controls[google.maps.ControlPosition.TOP].push(this.node_);
    
    // Bind this OverlayView to the map so we can access MapCanvasProjection
    // to convert LatLng to Point coordinates.
    this.setMap(map);
    
    // Register an MVC property to indicate whether this custom control
    // is visible or hidden. Initially hide control until mouse is over map.
    this.set('visible', false);
}
  
// Extend OverlayView so we can access MapCanvasProjection.
LatLngControl.prototype = new google.maps.OverlayView();
LatLngControl.prototype.draw = function() {};
  
/**
 * @private
 * Helper function creates the HTML node which is the control container.
 * @return {HTMLDivElement}
 */
LatLngControl.prototype.createHtmlNode_ = function() {
	var divNode = document.createElement("div");
	divNode.id = 'latlng-control';
	divNode.index = 100;
	divNode.style.visibility="hidden";
	divNode.style.background="#ffffff";
	divNode.style.border="1px solid #8888FF";

	divNode.innerHTML = '<div id="divElimina<? echo $nome; ?>" style="display:none;"><a href="javascript:eliminaPunto<? echo $nome; ?>(puntoSelezionato<? echo $nome; ?>);"><div class="contextMaps">&nbsp;&nbsp;Elimina questo punto&nbsp;&nbsp;<\/div><\/a></div>';
	if( '<? echo $tipo; ?>' == 'gmapsmulti') {
		divNode.innerHTML += '<div id="divAggiungi<? echo $nome; ?>" style="display:block;"><a href="javascript:aggiungiPuntoClick<? echo $nome; ?>();"><div class="contextMaps">&nbsp;&nbsp;Aggiungi questo punto&nbsp;&nbsp;<\/div><\/a></div>';
	} else {
		divNode.innerHTML += '<div id="divAggiungi<? echo $nome; ?>" style="display:block;"></div>';
	}
   	return divNode;
};
  
/**
 * MVC property's state change handler function to show/hide the
 * control container.
 */
LatLngControl.prototype.visible_changed = function() {
	this.node_.style.display = this.get('visible') ? '' : 'none';
};
  
/**
 * Specified LatLng value is used to calculate pixel coordinates and
 * update the control display. Container is also repositioned.
 * @param {google.maps.LatLng} latLng Position to display
 */
LatLngControl.prototype.updatePosition = function(latLng) {
	var projection = this.getProjection();
    var point = projection.fromLatLngToContainerPixel(latLng);
    
    var x=point.x;
    var y=point.y;
    /*
    if (x > maxX - 180) { x = maxX - 180 }
    if (y > maxY - 50) { y = maxY - 50 }
	*/		        
    
    lat<? echo $nome; ?> = latLng.lat();
    lng<? echo $nome; ?> = latLng.lng();
    
    // Update control position to be anchored next to mouse position.
    this.node_.style.left = x + this.ANCHOR_OFFSET_.x + 'px';
    this.node_.style.top = y + this.ANCHOR_OFFSET_.y + 'px';
    
};
  
// inzializzo questa mappa
addEvent(window,'load',initialize<? echo $nome; ?>);

					
