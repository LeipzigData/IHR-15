<?php
/**
 * main.php
 *
 * Stellt Anfragen an den Controller für das Eingabendiagramm.
 *
 * @author swp15-ihr Praktikumsgruppe Uni Leipzig
 */

/**
 * Controller holen
*/
include("modelcontroller.php");

// QueryWriter erzeugen
$contr = new QueryWriter();

// EndpointHandler erzeugen
$parse = new EndpointHandler();

// Variablen holen
$q = $_GET["q"];
$year = (int) $_GET["year"];

// Standard von $year
if ($year === 0) {
	$year = 2015;
}

// Ein- oder Ausgaben
//$contr->choose_amount setzen hier


// Falls erste Ringebene abgefragt
if ($q === "start" ) {
	echo($parse->sparqling($contr->firstRing($year),1));
}
else {
	$eingabe = "";
	
	// Ergebnishaushalt abfragen
	$arr = array('Ergebnishaushalt');
	
	// Eingabe vorbereiten fuer Transformation in eine SPARQL-Query
	$eingabe = explode(" ", $q);
	$eingabe = array_merge_recursive($arr, $eingabe);

	// Parsing
	echo($parse->sparqling($contr->sparqlTransformation($eingabe, $year),1));
}
?>
