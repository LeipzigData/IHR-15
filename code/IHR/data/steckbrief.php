<?php
/**
 * Stellt Anfragen an den Controller für die Produktsteckbriefe.
 *
 * @author swp15-ihr Praktikumsgruppe Uni Leipzig
 */

/**
 * Controller holen
*/
include("modelcontroller.php");

// QueryWriter erzeugen
$contr = new QueryWriter();

$q = $_GET["q"];
$qArray=explode("/",$q);
$Bezug=$qArray[count($qArray)-1];

$year = 2014;

$EndpointH=new Endpoint();

$rows = $EndpointH->query($contr->getInforamtion($q), 'rows');
$rows2= $EndpointH->query($contr->getSpielraum($Bezug), 'rows');
$err = $EndpointH->getErrors();

$result= $rows['result']['rows']['0']['l'];
$Spielraum=$rows2['result']['rows']['0']['grad'];

if($Spielraum<1.7) {
	$Spielraum = "klein";
}
elseif($Spielraum>=1.7&&$Spielraum<=2.3) {
	$Spielraum = "mittelmäßig";
}
elseif($Spielraum>2.3) {
	$Spielraum = "groß";
}

?>
<div class="region region-help alert alert-info">
	<h4>Name</h4>
	<div id="abtName" class="block-region"></div> 
	
	<h4 style="margin-top: 10px">Beschreibung</h4>
	<p><?php echo $result; ?></p> 	
	
	<h4 style="margin-top: 10px">Gestaltungsspielraum</h4>
	<p><?php echo $Spielraum; ?></p>
</div>
