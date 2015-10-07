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

$endpoint = 'http://pcai042.informatik.uni-leipzig.de:1524/sparql';
// QueryWriter erzeugen
$contr = new QueryWriter();

$q = $_GET["q"];
$qArray=explode("/",$q);
$Bezug=$qArray[count($qArray)-1];

$year = 2014;

$EndpointH=new Endpoint($endpoint);

$rows = $EndpointH->query($contr->getInforamtion($q), 'rows');
$rows2= $EndpointH->query($contr->getSpielraum($Bezug), 'rows');
$err = $EndpointH->getErrors();

$result= $rows['result']['rows']['0']['l'];
$Spielraum=$rows2['result']['rows']['0']['grad'];
if($Spielraum<1.7){$Spielraum="Gestaltungsspielraum: klein";}
elseif($Spielraum>=1.7&&$Spielraum<=2.3){$Spielraum="Gestaltungsspielraum: mittelmäßig";}
elseif($Spielraum>2.3){$Spielraum="Gestaltungsspielraum: groß";}

?>
<div class="region region-help alert alert-info">
    <i class="icon glyphicon glyphicon-question-sign" aria-hidden="true"></i>
	<h4>Name</h4>
	<div id="abtName" class="block-region"></div> 
	<h4>Beschreibung</h4>
	
	<div class="block-region"><?php echo $result; ?></div> 	
	<div class="block-region"><?php echo $Spielraum; ?></div> 	
</div>
