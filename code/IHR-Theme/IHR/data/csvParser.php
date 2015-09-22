<?php
  include("modelcontroller.php");
  class csvWriter{
   function writeCSV ($year){

      $parse = new EndpointHandler();

     // output headers so that the file is downloaded rather than displayed
       header('Content-Type: text/csv; charset=utf-8');
       header('Content-Disposition: attachment; filename=Haushalt.csv');

     // create a file pointer connected to the output stream
       $output = fopen('php://output', 'w');

    // output the column headings
       fputcsv($output, array('Column 1', 'Column 2', 'Column 3','Column 4'));

   // fetch the data
      
      $rows = $parse->sparqling(' PREFIX qb: <http://purl.org/linked-data/cube#>
                                  PREFIX ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> 
                                  SELECT distinct ?b xsd:string(?bn) xsd:decimal(?ein) xsd:decimal(?aus) 
                                  from <http://haushaltsrechner.leipzig.de/Data/EH_15G_Plan15/>
                                  from <http://haushaltsrechner.leipzig.de/Data/NeuesProduktModell/>
                                  WHERE {
                                  ?a a qb:Observation ; ihr:relatesTo ?b .
                                  optional { ?a ihr:aus ?aus . }
                                  optional { ?a ihr:ein ?ein . }
                                  optional { ?b rdfs:label ?bn . }
                                  }
                                  order by ?bn','CSV');

   // loop over the rows, outputting them
      // while ($row = mysql_fetch_assoc($rows)) 
             //fputcsv($output, $rows);
       var_dump($rows);
   }
  }

$foo = new csvWriter();
$foo->writeCSV(2015);

?>
