
sub fixNumber { 
# Umwandlung aus deutscher Darstellung 1.300,32 in internationale Darstellung
# 1300.32
  local $_=shift;
  s/\.//g;
  s/,/./g;
  return $_;
}

sub fixBezeichner { # Quote "
  local $_=shift;
  s/"/\\"/g;
  return $_;
}

sub TurtlePrefix() {
# Achtung - keine Leerzeile, das bringt das OntoWiki beim Hochladen aus dem
# Takt.
  return '@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix dct: <http://purl.org/dc/terms/> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
@prefix qb: <http://purl.org/linked-data/cube#> .
@prefix dbpedia: <http://dbpedia.org/ontology/> .
@prefix ihr: <http://haushaltsrechner.leipzig.de/Data/Model#> .
@prefix ihrdata: <http://haushaltsrechner.leipzig.de/Data/Observation/> .
@prefix ihrds: <http://haushaltsrechner.leipzig.de/Data/Dataset/> .

';
}

1;
