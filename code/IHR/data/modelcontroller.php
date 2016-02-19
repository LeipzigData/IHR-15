<?php
/**
 * Klassen für das Model und den Controller der MVC-Struktur.
 *
 * Funktionen und so
 * 
 * @author swp15-ihr Praktikumsgruppe Uni Leipzig
 */

/**
 *  Lib Sparql 1.1 HTTP Client 
 */
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
	public function sparqling ($model_query = null, $format) {
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
			if($format === 1)
		    {
			    //$result = '[["name", "Euro", "richtigername"]';
			    $result = '[["Abteilungsnummer", "Betrag", "Abteilung"]';
			    $cutStr = '';
			    $pos = 0;
			    $dual = 0;

 			    foreach ($rows["result"]["rows"] as $row) {
				    foreach($rows["result"]["variables"] as $variable) {
					
					    // Alles vor # bei den Produktbezeichnungen abschneiden
					    if($dual == 0) {
						    $cutStr = '';
						    $pos = strpos($row[$variable], '#');
						    $cutStr = substr($row[$variable], ($pos + 1));					
						    $result = $result . ',["' .$cutStr . '",'; 
						    $dual++;	
					    }
					
					    // Geldwerte
					    elseif( $dual == 1 ) {
						    $result = $result . ($row[$variable]) . ','; 
						    $dual++;
					    }
					
					    // Klarnamen
					    else {
					    	$result = $result . '"' . str_replace('"','--',$row[$variable]) . '"]'; 
						    $dual = 0;
					    }
					
				    }
 			    }
			    return $result = $result . ']';
		    }
			else if ($format === 3)
			{
				$result = "";
				$pos = 0;
				foreach ($rows["result"]["rows"] as $row) {
					foreach($rows["result"]["variables"] as $variable) {
						$pos = strpos($row[$variable], '#');
                        $result = $result . substr($row[$variable], ($pos + 1)) . "/";
                    }
                }
                return $result;
            }
			
			// CSV generieren
			else if ($format === "CSV")
			{
				$result = '';
				$variablecount = count($rows["result"]["variables"]);
				
				// Header
				for ($i = 0; $i < $variablecount; $i++)
				{
					$result .= $rows["result"]["variables"][$i];
					
					if ( $i === (count($rows["result"]["variables"])-1) )
					{
						$result .= "\n";
					}
					else 
					{
						$result .= '|';
					}
				}
				
				// Inhalt
				for ($i = 0; $i < count($rows["result"]["rows"]); $i++)
				{
					$j = 0;
					foreach ($rows["result"]["variables"] as $variable)
					{
						$result .= $rows["result"]["rows"][$i][$variable];
						
						if ($j != ($variablecount-1))
						{
							$result .= '|';
						}
						$j++;
					}
		
					
					if ( $i != (count($rows["result"]["rows"])-1) )
					{
						$result .= "\n";
					}
				}
				
				return $result;
			}
			
			else if ($format === "JSON")
			{
				$return = "[\n";	
				
				for ($j = 0; $j < count($rows["result"]["rows"]); $j++)
				 {
					
					$return .= "{\n";
					
					for ($i = 0; $i < count($rows["result"]["variables"]); ++$i)
					{
						if ($i)
						{
							$return .= ",\n";
						}						
						
						$return .= "\"" . $rows["result"]["variables"][$i] . "\": \"" . $rows["result"]["rows"][$j][$rows["result"]["variables"][$i]] . "\"";						
						
						if ($i === (count($rows["result"]["variables"])-1))
						{
							$return .= "\n";
						}
					}
					
					$return .= "}";
					
					if ($j != (count($rows["result"]["rows"])-1))
					{
						$return .= ",";
					}
					
					$return .="\n";
				
				}
				
				$return .= "]";
				return $return;
			}
			else
		    {	
               return $rows;
			}
		}
    }
}

/**
 * Klasse zum Schreiben der SPARQL-Queries
 */
class QueryWriter {
	
	/**
	 * Hier werde die Prefixe fuer die Sparql-Abfrage definiert. Dabei wird rdfs fuer die Labels (Klarnamen) benoetigt. 
	 * Das Praefix xsd wird zum Typcasten der Strings benoetigt, qb fuer die Zeitangabe.
	 */
	private $prefix = 'PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>  PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX xsd:<http://www.w3.org/2001/XMLSchema#> PREFIX qb: <http://purl.org/linked-data/cube#>';
	
	/**
	 * Unsere URI -- obsolet, wird aber auch nicht verwendet. HGG, 2016-02-19
	 */
	private $ihr_uri = ' PREFIX ihr: <http://pcai042.informatik.uni-leipzig.de/~swp15-ihr/leipzighaushalt#> ';
	/**
	*Stellt die From Klausel unserer Queryabfrage dar
	**/
	private $sparql_from = 'http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan15/';
	/**
	 * Attribute des Haushalts, welche fuer die Anfrageerstellung benoetigt wird. 
	 */ 
	private $attribute_array = array('kategorie','prBerNummer','prGrNummer','prUGrNummer','bezugNummer');
	
	/**
	 * Diese Array enthaelt die Attribute fuer die Einnahmen und Ausgaben. Je nach Modus von 
	 * choose_amount wird Einnahmen oder Ausgaben gewaehlt. 
	 */
	private $amount = array('ein', 'aus');
	
	/**
	 * Dieses Variable bestimmt, ob die Einnahmen oder Ausgabe genommen werden. 
	 * Dabei kann die Variable entweder nur eins oder null annehmen
	 */
	private $choose_amount = 0;

	/**
	 * Diese Funktion wandelt den Pfad in eine Sparql-Abfrage um. Dabei werden die oben defininierten Praefixe
	 * verwendet. 
	 * 
	 * @param	string	$category	Erwartet eine Stringarray
	 * @param	int	$year	Jahreszahl	
	 *
	 * @return	Sparql-Query als String
	 */
	function sparqlTransformation(array $category, $year = 2015) {
                 $kategorien = array("PrBer", "PrGr", "PrUGr","Bezug1100");

    $input = str_replace($kategorien,"",$category[count($category)-1]);

    $query_controller = 'PREFIX qb: <http://purl.org/linked-data/cube#>

                         PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 

                         SELECT distinct ?b as ?prBerNummer xsd:decimal(?'. ($this->amount[$this->choose_amount]) .') as ?endsumme xsd:string(?bn) as ?label

                         from <'.($this->sparql_from).'>

                         from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>

                         WHERE {
									
                                   ?a a qb:Observation ; ihr:relatesTo ?b .';

    

    if(strstr($category[count($category)-1],'PrGr') != null ){

         $query_controller =  $query_controller.'optional { ?a ihr:'. ($this->amount[$this->choose_amount]) .' ?'. ($this->amount[$this->choose_amount]) .' . }

                                                 optional { ?b rdfs:label ?bn . }

                                                 filter regex(?b,"PrUGr'.$input.'")

                                                 }

                                                 order by ?bn';

    }

    

      if(strstr($category[count($category)-1],'PrBer') != null ){

         $query_controller =  $query_controller.'optional { ?a ihr:'. ($this->amount[$this->choose_amount]) .' ?'. ($this->amount[$this->choose_amount]) .' . }

                                                 optional { ?b rdfs:label ?bn . }

                                                 filter regex(?b,"PrGr'.$input.'")

                                                 }

                                                 order by ?bn';

    }

    

   

      if(strstr($category[count($category)-1],'PrUGr') != null ){

         $query_controller =  $query_controller.'optional { ?a ihr:'. ($this->amount[$this->choose_amount]) .' ?'. ($this->amount[$this->choose_amount]) .' . }

                                                 optional { ?b rdfs:label ?bn . }

                                                 filter regex(?b,"Bezug1100'.$input.'")

                                                 }

                                                 order by ?bn';

      }

                               

    return $query_controller;
	/**	
		// Check ob zu viele Kategorien angegeben
		if(count($category) > count($this->attribute_array)) {
			return false;
		}
		else {
			$controller_query = ' SELECT ?' . ($this->attribute_array[count($category)] ) . ' SUM(xsd:decimal(?' . ($this->amount[$this->choose_amount]) .')) AS ?endsumme ?label 
			WHERE { 
				?x ';
	 
			for($i = 0;  $i < count($category); $i++) {
			   $controller_query .=  'ihr:' . ($this->attribute_array[$i]) . ' ihr:' . $category[$i] . ' ; ';
			}
	 
			$controller_query .= ' ihr:' . ($this->attribute_array[count($category)] ) . ' ?' . ($this->attribute_array[count($category)] ) . ' ;
						  ihr:' . ($this->amount[$this->choose_amount]) . ' ?' . ($this->amount[$this->choose_amount]) . '; qb:dataSet ihr:haushalt' . strval($year) . '. 
						  ?' . $this->attribute_array[count($category)] . ' rdfs:label ?label.
					} 
					GROUP BY ?' . ($this->attribute_array[count($category)] ) . ' ?label 
					HAVING (SUM(xsd:decimal(?'. ($this->amount[$this->choose_amount]) .')) != 0)
					ORDER BY ?' . ($this->attribute_array[count($category)] );
		   
			return ($this->prefix) . ($this->ihr_uri) . $controller_query;
		}**/
	}
  
	/**
	 * Eingaben oder Ausgaben ausgeben?
	 * Diese Funktion stellt den Modus fuer die Trennung zwischen Einnahmen und Ausgabe.
	 * Ist er auf er auf Null gestellt, so gibt er die Einnahmen der Kategorie ausgeben (Standardmodus).
	 * Ist er auf Eins gestellt, gibt er die Ausgabe der Kategorie aus. 
	 */  
	function setAmount() {
		if($this->choose_amount != 1) {
			$this->choose_amount = 1;
		} 
		else {
			$this->choose_amount = 0;
		}
	}
  
	/**
	 * Diese Methode erzeugt eine Sparql-Query. Durch diese wird die oberste Kategorie aus dem 
	 * Cude ausgelesen und die Zahlen werden aufsummiert. Dabei wird entweder die Einnahmen oder
	 * die Ausgabe ausgelesen, je nachdem auf welchen Modus choose_amount gestellt ist. Es werden
	 * die Kategorie, die entsprechenden Zahlen und die Klarnamen der Kategorie ausgeben.
	 * 
	 * @param	int	$year	Jahreszahl 
	 * @return SPARQL-Query als String
	 */
	function firstRing($year = 2015) {
     		     $controller_query = 'PREFIX qb: <http://purl.org/linked-data/cube#>
 PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
 SELECT distinct ?b as ?prBerNummer  xsd:decimal(?'.($this->amount[$this->choose_amount]).') AS ?endsumme xsd:string(?bn) as ?label
 from <'.($this->sparql_from).'>
 from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>
 WHERE {
 ?a a qb:Observation ; ihr:relatesTo ?b .
 optional { ?a ihr:'.($this->amount[$this->choose_amount]).' ?'.($this->amount[$this->choose_amount]).' . }
 optional { ?b rdfs:label ?bn . }
 filter regex(?b,"PrBer")
 }
order by ?bn';
                return  $controller_query ;    
	}
	
	
                              	
	
	/**
	* Gibt die beiden From Klausen aus.
	* @return die beiden From Klauseln
	**/
	function getFrom(){
	  return $this->sparql_from;
	}

	 function getInforamtion($bezug){

	 	$teile = explode("/",$bezug);

	 	$bezugsnumber = $teile[count($teile) -1];

        $ihrQuery = '  PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#>  

                  select distinct ?u  ?l

	               from <http://haushaltsrechner.leipzig.de/Data/Produktsteckbriefe/>

                   WHERE{ 

	                   ?u a ihr:Produktsteckbrief; ihr:Kurzbeschreibung ?l . 

                             filter regex(?u,"'.$bezugsnumber.'")

                    }';

  return ($this->prefix).$ihrQuery;

 }



  function searchLabel($term){

      $ihrQuery = ' PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#>  

                    select distinct strafter(xsd:string(?u),"#") ?label  ?details

                    from <http://haushaltsrechner.leipzig.de/Data/Produktsteckbriefe/>

                    from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>

                     WHERE {

                        { ?u rdfs:label ?label . 

                          ?x  a ihr:Produktsteckbrief; ihr:Kurzbeschreibung ?details .

                          filter regex(?x,strafter(xsd:string(?u),"#"))

                        } 

                        union {

                          ?u rdfs:label ?label .

                        }

                         filter regex(?label,"'.$term.'")

                        }

                        Group by ?u';

     return ($this->prefix).$ihrQuery;

  }

  

  function searchNumbers($bezug){

        $ihrQuery = 'PREFIX qb: <http://purl.org/linked-data/cube#>

                     PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 

                     SELECT distinct ?b xsd:decimal(?ein) xsd:decimal(?aus) 

                     from <'.$this->sparql_from.'>

                     from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>

                     WHERE {

                            ?a a qb:Observation ; ihr:relatesTo ?b .

                            optional { ?a ihr:aus ?aus . }

                            optional { ?a ihr:ein ?ein . }

                            FILTER regex(strafter(xsd:string(?b),"#"),"'.$bezug.'")

                      }';

		return ($this->prefix).$ihrQuery;

   }

 function getPath($number){

	 $kategorien = array("PrBer", "PrGr", "PrUGr","Bezug1100");

	 $input = str_replace($kategorien,"",$number);

     $query = ' PREFIX qb: <http://purl.org/linked-data/cube#>

                PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 

                SELECT distinct ?b 

                from <'.($this->sparql_from).'>

                from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>

                WHERE {{

                        ?a a qb:Observation ; ihr:relatesTo ?b .

                        filter regex(?a, "PrBer'.substr($input,0,2).'")

                        }';

	if((strstr($number,'PrGr') != null)) {

		$query = $query.'union{

                         ?a a qb:Observation ; ihr:relatesTo ?b .

                          filter regex(?a, "PrGr'.substr($input,0,3).'")

                      }';

	}

	

	if((strstr($number,'PrUGr') != null)) {

		$query = $query.'union{

                         ?a a qb:Observation ; ihr:relatesTo ?b .

                          filter regex(?a, "PrGr'.substr($input,0,3).'")

                      }  union{

                      ?a a qb:Observation ; ihr:relatesTo ?b .

                       filter regex(?a, "PrUGr'.substr($input,0,4).'")

                }';

	}

	

	if((strstr($number,'Bezug') != null)) {

		$query = $query.'union{

                         ?a a qb:Observation ; ihr:relatesTo ?b .

                          filter regex(?a, "PrGr'.substr($input,0,3).'")

                      }  union{

                      ?a a qb:Observation ; ihr:relatesTo ?b .

                       filter regex(?a, "PrUGr'.substr($input,0,4).'")

                

				}  union{

                      ?a a qb:Observation ; ihr:relatesTo ?b .

                       filter regex(?a, "'.$number.'")

                }';

	}

	

	$query = $query.' }';

	return $query;

   }
    function setFrom($year = 2015){

  $parse = new EndpointHandler();

  $rows = $parse->sparqling('prefix ihr: <http://haushaltsrechner.leipzig.de/Data/Model#>

                      SELECT ?a ?b 

                                  from <http://haushaltsrechner.leipzig.de/Data/Config/>



                                  WHERE



                                  {



                                          ?a  ihr:jahr "'.$year.'"; ihr:Cube ?b.



                                  }',0);
   if( count( $rows['result']['rows']) == 0){
     $this->sparql_from = 'http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan15/';
   }
    $this->sparql_from = $rows['result']['rows'][0]["b"];

 } 

  function getCube($year = 2015 ){
      $query = ' PREFIX qb: <http://purl.org/linked-data/cube#>
                                  PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
                                  SELECT distinct ?b xsd:string(?bn) xsd:decimal(?ein) xsd:decimal(?aus) 
                                  from <'.($this->sparql_from).'>
                                  from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>
                                  WHERE {
                                  ?a a qb:Observation ; ihr:relatesTo ?b .
                                  optional { ?a ihr:aus ?aus . }
                                  optional { ?a ihr:ein ?ein . }
                                  optional { ?b rdfs:label ?bn . }
                                  }
                                  order by ?bn';
   return $query;
  }

   function searchData($term){
  $query = 'PREFIX qb: <http://purl.org/linked-data/cube#>
            PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
            SELECT distinct ?b as ?prBerNummer xsd:string(?bn) as ?label xsd:decimal(?ein) as ?Einnahme  xsd:decimal(?aus) AS ?Ausgabe
            from <'.$this->sparql_from.'>
            from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>
            WHERE {
                ?a a qb:Observation ; ihr:relatesTo ?b .
                optional { ?a ihr:aus ?aus . }
                optional { ?a ihr:ein ?ein . }
                optional { ?b rdfs:label ?bn . }
                filter regex(?bn,"'.$term.'")
           }'; 
   return $query; 
  }

  function getSpielraum($number){

	 $query = 'PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#>

	           SELECT  ?grad 

               FROM <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>

               WHERE {

                      ?x ihr:hatGrad ?grad.

                       filter regex(?x,"'.$number.'")}';

	 return $query;

 } 
} 





?>
