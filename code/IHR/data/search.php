<?php
/**
 * main.php
 *
 * @author swp15-ihr Praktikumsgruppe Uni Leipzig
 */

// Controller holen
include("modelcontroller.php");

// QueryWriter erzeugen
$contr = new QueryWriter();

// CubeParser erzeugen
$parse = new EndpointHandler();

// Variablen holen
$q = $_GET["q"];
// Parsing
if(strcmp(substr($q,-1),"1") == 0) 
{
$result = '[["Posten","Label","Einnahmen","Ausnahmen"]';
$cutStr = '';
$pos = 0;
$dual = 0;

$rows =	($parse->sparqling($contr->searchData(substr($q,0,-1)),8)); 
 	foreach ($rows["result"]["rows"] as $row) {
		foreach($rows["result"]["variables"] as $variable) {
				if($dual == 0) {
			    $cutStr = '';
				$pos = strpos($row[$variable], '#');
				$cutStr = substr($row[$variable], ($pos + 1));					
				$result = $result . ',["' .$cutStr . '",'; 
				$dual++;	
				}  
				else if( $dual == 1 ) {
					$result = $result . '"' . str_replace('"','--',$row[$variable]) . '",'; 
					$dual++;
					}
					
					else if($dual == 2 || $dual == 3) {
					$result = $result . ($row[$variable]) . ','; 
					$dual++;
					if($dual > 3)
					{
						$dual = 0;
						$result = substr($result,0,-1);
						$result = $result . ']';
					}

					}
		}
	
	}
$result = $result . ']';
echo $result; 
}
else if(strcmp(substr($q,-1),"0") == 0)
{
echo  ($parse->sparqling($contr->getPath(substr($q,0,-1)),3)) ;
}
?>