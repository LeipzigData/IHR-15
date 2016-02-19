<?php
include("modelcontroller.php");

function jahresvergleich($term){
	$query_controller = 'PREFIX qb: <http://purl.org/linked-data/cube#>
                         PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
                         select distinct ?b ?j xsd:decimal(?ein) xsd:decimal(?aus) ?l
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
                                ?x rdfs:label ?l .
                                filter regex(?b,"'.$term.'$")
                                filter regex(?x,"'.$term.'$")  
                        }
                        order by ?l ?j';
	return $query_controller;
}


// QueryWriter erzeugen
$contr = new QueryWriter();

$q = $_GET["q"];

$EndpointH = new Endpoint();

$rows = $EndpointH->query(jahresvergleich($q), 'rows');
$err = $EndpointH->getErrors();
if ($err) {
	print_r($err);
	throw new Exception(print_r($err,true));
}

$result='';
$result.='<table id="JVTable">'; 
$result.='<thead> <tr><th> Name </th> <th> Jahr</th><th> Einnahmen </th> <th> Ausgaben </th></tr></thead>';
$result.='<tbody>';

foreach ($rows['result']['rows'] as $row)
{	
	$result .= '<tr> <td>' . $row['l'].' </td> <td>'.$row['j']*(1).' </td><td class="numForm">'.$row['callret-2'].' </td><td class="numForm">'.$row['callret-3']*(-1).' </td> </tr>';
}  
$result.='</tbody>';
$result.='</table>';


echo ($result); 
?>
