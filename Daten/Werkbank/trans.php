<?php
/**
 * User: Hans-Gert Gräbe
 * Date: 12.02.2015
 */

require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

function setNamespaces() {
  EasyRdf_Namespace::set('ihr', 'http://haushaltsrechner.leipzig.de/Data/Model#');
  EasyRdf_Namespace::set('dct', 'http://purl.org/dc/terms/');
}

function sparqlQuery($query,$endpoint) {
  $sparql = new EasyRdf_Sparql_Client($endpoint);
  try {
    $results = $sparql->query($query);
  } catch (Exception $e) {
    echo $e->getMessage();
  }
  return $results;
}

// -- different tests

function testSerialization() {
  $graph = new EasyRdf_Graph("http://haushaltsrechner.leipzig.de/Data/Bezugliste/");
  $graph->parseFile("Bezugliste.ttl");
  echo $graph->serialise("turtle");
}

function testSelectQuery() {
  $endpoint='http://localhost:8890/sparql';
  $query='
PREFIX qb: <http://purl.org/linked-data/cube#>
PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
SELECT distinct ?bn ?ugn ?gn ?n
from <http://haushaltsrechner.leipzig.de/Data/HaushaltLeipzig2014/>
WHERE {
?a a qb:Observation .
?a ihr:bezugNummer ?bn ; 
ihr:prBerNummer ?n ; ihr:prGrNummer ?gn ; 
ihr:prUGrNummer ?ugn  . 
}
order by ?bn
';
  $results=sparqlQuery($query,$endpoint);
  print_r($results);
}

function testConstructQuery() {
  /* Der Virtuoso SPARQL Endpunkt gibt in der Standardeinstellung maximal
     10.000 Tripel zurück. Eine Construct-Query dauert hier auch ziemlich
     lange. */
  $endpoint='http://localhost:8890/sparql';
  $query='
PREFIX qb: <http://purl.org/linked-data/cube#>
PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
CONSTRUCT { 
?a a qb:Observation ;
ihr:kategorie ihr:Ergebnishaushalt ;
ihr:bezugNummer ?bn ;
ihr:summe ?s .
}
from <http://haushaltsrechner.leipzig.de/Data/HaushaltLeipzig2014/>
WHERE {
?a a qb:Observation ; 
ihr:kategorie ihr:Ergebnishaushalt ;
ihr:bezugNummer ?bn ;
ihr:summe ?s .
}';
  $results=sparqlQuery($query,$endpoint);
  //print_r($results);
  setNamespaces(); 
  echo $results->serialise("turtle");
}

//setNamespaces(); testSerialization();
//testSelectQuery();
testConstructQuery(); 

?>


