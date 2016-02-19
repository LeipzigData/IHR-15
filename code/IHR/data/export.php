<?php
/**
 * Stellt Anfragen an den Controller für den Datenexport.
 *
 * @author swp15-ihr Praktikumsgruppe Uni Leipzig
 */

/**
 * Controller holen
*/
include("modelcontroller.php");

// Standardwert fuer Jahr festlegen
$year = ($_GET["year"] ? (int) $_GET["year"] : 2015);

$mode = ($_GET["mode"] ? $_GET["mode"] : "CSV");

$parse = new EndpointHandler();

$query = 'PREFIX qb: <http://purl.org/linked-data/cube#>
                         PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
                         select distinct ?b as ?Produkt ?j as ?jahr xsd:decimal(?ein) as ?einnahme xsd:decimal(?aus) as ?ausgabe ?l as ?bezeichnung
                         from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan14/>
                         from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan15/>
                         from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan16/>
                         from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan17/>
                         from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan18/>
                         from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan19/>
                         from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>
                         WHERE {
                                ?u a qb:Observation ; ihr:relatesTo ?b; 
                                ihr:ein ?ein; ihr:aus ?aus; ihr:jahr ?j .            
                                ?b rdfs:label ?l .
                                filter regex(?j,"^' . $year . '")
                        }
                        order by ?l ?j';
						
						
if ($mode === "CSV") {						
	// output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Haushalt.csv');

	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');		
}	

						
echo($parse->sparqling($query, $mode));
?>
