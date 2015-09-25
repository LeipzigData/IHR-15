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



$a = ($parse->sparqling($contr->searchNumbers('Bezug1100261003'),2)); 

echo "</br>";
echo $a['result']['rows'][0]['b'] ;
echo "</br>";
echo $a['result']['rows'][0]['callret-1'] ;
echo "</br>";
echo $a['result']['rows'][0]['callret-2'] ;


 foreach ($a ['result']["rows"] as $row) {
//  echo "</br>";
//  echo $a['result']['rows']['b'] ;

        foreach($a["result"]["variables"] as $variable) {

//      echo $variable;

 }}

?>
