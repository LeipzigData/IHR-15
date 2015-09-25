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
echo($parse->sparqling($contr->searchData(substr($q,0,-1)),1)); 
}
else if(strcmp(substr($q,-1),"0") == 0)
{
echo($parse->sparqling($contr->getPath(substr($q,0,-1)),0));
}
?>