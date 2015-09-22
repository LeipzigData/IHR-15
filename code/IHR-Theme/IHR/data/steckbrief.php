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

$year = 2014;

$EndpointH=new Endpoint($endpoint);

$rows = $EndpointH->query($contr->getInforamtion($q), 'rows');
$err = $EndpointH->getErrors();

$result='';
$result.='<div class="bg-info" style="padding: 5px;"><h4>Beschreibung:</h4>'.$rows['result']['rows']['0']['l'] . '</div>';
echo $result;
?>
