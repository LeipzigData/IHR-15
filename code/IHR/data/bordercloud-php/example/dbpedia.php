<?php

require_once('../Endpoint.php');

    $endpoint ="http://pcai042.informatik.uni-leipzig.de:1524/sparql";
    $sp_readonly = new Endpoint($endpoint);
 $q = "
PREFIX ihr: <http://pcai042.informatik.uni-leipzig.de/~swp15-ihr/leipzighaushalt#>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

SELECT  ?prBerNummer SUM(xsd:decimal(?rechnungsendbetrag)) AS ?endsumme
                WHERE {
                  ?x ihr:kategorie ihr:Ergebnishaushalt; ihr:prBerNummer ?prBerNummer;
                     ihr:rechnungsendbetrag ?rechnungsendbetrag.
               } 
               GROUP BY ?prBerNummer
               ORDER BY ?prBerNummer
             ";



 $rows = $sp_readonly->query($q, 'rows');
 $err = $sp_readonly->getErrors();



 if ($err) {
      print_r($err);
      throw new Exception(print_r($err,true));
    }

 foreach($rows["result"]["variables"] as $variable){
        printf($variable);
        echo '|';
 }
 echo "\n";

 foreach ($rows["result"]["rows"] as $row){
        foreach($rows["result"]["variables"] as $variable){
                printf($row[$variable]);
        echo '|';
        }
        echo "\n";
 }
?>

