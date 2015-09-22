<?php
/**
 * User: Hans-Gert Gräbe
 * Date: 08.08.2015
 * Demoausgabe einer Jahresübersicht 
 */

require_once("lib/EasyRdf.php");

// output settings
//=========================
ini_set('default_charset', 'utf-8');

function setNamespaces() {
  EasyRdf_Namespace::set('ihr', 'http://haushaltsrechner.leipzig.de/Data/Model#');
  EasyRdf_Namespace::set('dct', 'http://purl.org/dc/terms/');
  EasyRdf_Namespace::set('qb', 'http://purl.org/linked-data/cube#');
}

function queryData() {
  $query='
PREFIX qb: <http://purl.org/linked-data/cube#>
PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
Construct { ?a ?b ?c .} 
from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan16/>
where { ?a a qb:Observation ; ihr:relatesTo ?r . ?a ?b ?c .
filter regex(?r,"PrBer")}
';
  $sparql = new EasyRdf_Sparql_Client('http://pcai042.informatik.uni-leipzig.de:1524/sparql');
  $graph=$sparql->query($query); // a CONSTRUCT query returns an EasyRdf_Graph
  $out="Bereich|Einnahmen|Ausgaben\n";
  foreach ($graph->allOfType('qb:Observation') as $v) {
    $out.=addLine($v);
  }
  return $out;
}

function queryData1() {
  $endpoint='http://pcai042.informatik.uni-leipzig.de:1524/sparql';
  $query='
PREFIX qb: <http://purl.org/linked-data/cube#>
PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
Select distinct ?bereich xsd:decimal(?ein) as ?einnahmen xsd:decimal(?aus) as ?ausgaben
from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan16/>
where { ?a a qb:Observation ; ihr:relatesTo ?bereich ; ihr:ein ?ein; ihr:aus ?aus . 
filter regex(?bereich,"PrBer")}

';
  $call=$endpoint.'?default-graph-uri=&query='.urlencode($query).'&format=csv';
  return file_get_contents($call);
}

function addLine($v) {
  $ein=adjustNumber($v->join('ihr:ein'));
  $aus=adjustNumber($v->join('ihr:aus'));
  $rel=$v->get('ihr:relatesTo');
  $out=fix("$rel|$ein|$aus");
  return "$out\n";
}

function fix($s) {
  $s=str_replace('http://haushaltsrechner.leipzig.de/Data/Model#',"",$s);
  return $s;
}

function adjustNumber($s) {
  return number_format($s,2,",",".");
}

setNamespaces();
echo queryData1();

?>


