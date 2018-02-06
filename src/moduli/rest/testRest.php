<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
	<head>
  		<title>Test del servizio rest</title>
  		<script type="text/javascript" src="../../grafica/jquery-1.8.2.min.js"></script>
 	</head>
 	<body>
 		<div>
 			<label for="url">Inserisci l'URL per la chiamata REST</label><br />
			<input type="text" id="url" name="url" value="" style="width: 500px;" />
			<input type="button" id="btn" name="btn" value="Invia" />
		</div>
		<br /><br /><br />
		<div>
			Response<br /><br />
			<div id="response">
				
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#btn').click(function() {
					console.log(jQuery('#url').val());
					jQuery.ajax({
				        url : jQuery('#url').val(),
				        type: "GET",
				        dataType: "json",
				        success:function(data, textStatus, jqXHR) {
				            jQuery('#response').html('ok');
				        },
				        error: function(jqXHR, textStatus, errorThrown) {
				            jQuery('#response').html('ERRORE: '+errorThrown);
				        }
				    });
				});
			});
		</script>
 	</body>
</html>