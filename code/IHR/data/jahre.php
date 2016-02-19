<?php
/**
 * Gibt die Auswahl an Jahren aus der config.ttl zurück.
 *
 * @author swp15-ihr Praktikumsgruppe Uni Leipzig
 */

/**
 * Controller holen
*/
include("modelcontroller.php");

$EndpointH = new Endpoint();

$query = 'PREFIX qb: <http://purl.org/linked-data/cube#>
                         PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
                         select distinct ?j as ?jahr
                         from <http://haushaltsrechner.leipzig.de/Data/Config/>
                        WHERE {
                                ?u a ihr:ConfigEntry; ihr:jahr ?j .
                        }
                        order by ?j';


$rows = $EndpointH->query($query, 'rows');
$err = $EndpointH->getErrors();
if ($err) {
	print_r($err);
	throw new Exception(print_r($err,true));
}
$result = '';

foreach ($rows['result']['rows'] as $row)
{	
	$result .= $row['jahr'] . ",";
}  

echo $result;
?>