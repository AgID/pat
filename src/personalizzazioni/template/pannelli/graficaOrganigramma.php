<div>
<?
if($entePubblicato['file_organigramma'] != '' and file_exists($uploadPath."/enti_pat/".$entePubblicato['file_organigramma'])) {
	?>
	<script src="<? echo $server_url;?>grafica/lightbox2/js/lightbox-2.6.min.js"></script>
	<link href="<? echo $server_url;?>grafica/lightbox2/css/lightbox.css" rel="stylesheet" />
	<a href="<? echo $server_url.$uploadPath."/enti_pat/".$entePubblicato['file_organigramma'];?>" data-lightbox="Organigramma" title="Organigramma">
		<img src="<? echo $server_url.$uploadPath."/enti_pat/".$entePubblicato['file_organigramma'];?>" alt="Organigramma" title="Organigramma" width="195" />
	</a>
	<?
}
?>
</div>