<html>
 <head>
  <meta charset="UTF-8">
  <title>Titel der Seite</title>
 </head>
<body>

<?php

include_once("bordercloud-php/Endpoint.php");

/**
 * Klasse zum Parsen des RDF Cubes
 */
class EndpointHandler {
	/**
	 * Adresse des Endpunkts festlegen
	 */
	private $endpoint = 'http://pcai042.informatik.uni-leipzig.de:1524/sparql';
	
	/**
	 * Variable für den offenen Endpunkt
	 */
	private $sp_readonly = null;
	
	/**
	 * Konstruktor für EndpointHandler
	 */
	public function EndpointHandler() {
		
		$this->sp_readonly = new Endpoint($this->endpoint);
	}

	/**
	 * Funktion zum Stellen von Anfragen an den SPARQL-Endpunkt.
	 *
	 * @param string $model_query   Übergabe einer SPARQL-Anfrage in Stringform.
	 *
	 * @return $result
	 */
	public function sparqling ($model_query = null) {
		$err = '';
		
		if($model_query != null) {
			$rows = $this->sp_readonly->query($model_query, 'rows');
			$err = $this->sp_readonly->getErrors();
		}
		
		// Error Handling
		if ($err || $model_query === null) {
			print_r($err);
			return false;
	    }

		// Cube im JSON-Format parsen
		else {
			//$result = '[["name", "Euro", "richtigername"]';
			$result = '[';
			$cutStr = '';
			$pos = 0;
			$dual = 0;

 			foreach ($rows["result"]["rows"] as $row) {
				foreach($rows["result"]["variables"] as $variable) {
					
					// Alles vor # bei den Produktbezeichnungen abschneiden
					/*if($dual == 0) {
						$cutStr = '';
						$pos = strpos($row[$variable], '#');
						$cutStr = substr($row[$variable], ($pos + 1));					
						$result = $result . ',"' .$cutStr . '",'; 
						$dual++;	
					}
					
					// Geldwerte
					elseif( $dual == 1 ) {
						$result = $result . ($row[$variable]) . ','; 
						$dual++;
					}
					
					// Klarnamen
					else {
						$result = $result . '"' . str_replace('"','--',$row[$variable]) . '"'; 
						$dual = 0;
					}*/
					if((strlen($row[$variable])))
					$result = $result . '"' .  str_replace('"','&quot',$row[$variable]) . '",';
					
				}
 			}
			$result = substr($result, 0, (strlen($result)-1));
			$result = $result . ']';
			return  htmlspecialchars($result  , ENT_COMPAT, 'UTF-8');
		}
    }
}
 class search{
	 private $prefix = 'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>  PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX xsd:<http://www.w3.org/2001/XMLSchema#> PREFIX qb: <http://purl.org/linked-data/cube#>';
	 private $ihr_uri = ' PREFIX ihr: <http://pcai042.informatik.uni-leipzig.de/~swp15-ihr/leipzighaushalt#> ';
	 
	 function getLabels(){
		 $query = "SELECT DISTINCT ?label WHERE {
                      ?x rdfs:label ?label.
                      }" ;
		return ($this->prefix).($this->ihr_uri).$query;
	 }
 }

 


// QueryWriter erzeugen
$s = new search();
// CubeParser erzeugen
$parse = new EndpointHandler();

// Variablen holen
// Parsing
echo($parse->sparqling( $s->getLabels()));
?>


<!DOCTYPE html>

</body>
</html>