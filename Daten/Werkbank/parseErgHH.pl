# 2015-08-14, HGG: Neue Version, die auch PSP einzeln erfasst und zu SSP als
# Ebene 4 aggregiert.
# 2015-08-20, HGG: Ebene ihr:Top ergänzt
# 2015-08-20, HGG: Anmerkungen von Frau Vogt zu interner Leistungsverrechnung ergänzt

use Helper;

undef $/;
open(FH,shift) or die;
$_=<FH>;
close FH;
my $hash;
my $l11hash;
my $basis="EH_16Ratsinfo";
my $jahr="2016";
#map { processRatsinfoLine($_); } split /\n/;
#map { processRatsinfo14Line($_); } split /\n/;
map { processLine($_); } split /\n/;
#print createOutput();
#print TurtlePrefix().OntoPrefix().createOutput();

### end main

sub processRatsinfoLine {
# bezieht sich auf Datei Ratsinfo* in 2015/16
  local $_=shift;
  my @l=split /\|/;
  my $psp=getPSP($l[5]);
  my $ssp=getSSP($psp);
  my $pugr=getPrUGr($psp);
  my $pgr=getPrGr($psp);
  my $pb=getPrBer($psp);
  my $ein=fixNumber($l[8]);
  my $aus=fixNumber($l[9]);
  $hash->{$psp}{"type"}="PSP";
  $hash->{$psp}{"ein"}+=$ein;
  $hash->{$psp}{"aus"}+=$aus;
  $hash->{$ssp}{"type"}="Bezug";
  $hash->{$ssp}{"ein"}+=$ein;
  $hash->{$ssp}{"aus"}+=$aus;
  $hash->{$pugr}{"type"}="PrUGr";
  $hash->{$pugr}{"ein"}+=$ein;
  $hash->{$pugr}{"aus"}+=$aus;
  $hash->{$pgr}{"type"}="PrGr";
  $hash->{$pgr}{"ein"}+=$ein;
  $hash->{$pgr}{"aus"}+=$aus;
  $hash->{$pb}{"type"}="PrBer";
  $hash->{$pb}{"ein"}+=$ein;
  $hash->{$pb}{"aus"}+=$aus;
  $hash->{"Top"}{"type"}="";
  $hash->{"Top"}{"ein"}+=$ein;
  $hash->{"Top"}{"aus"}+=$aus;
}

sub processRatsinfo14Line {
# bezieht sich auf Datei Ratsinfo* in 2014
  local $_=shift;
  my @l=split /\|/;
  my $psp=getPSP($l[4]);
  my $ssp=getSSP($psp);
  my $pugr=getPrUGr($psp);
  my $pgr=getPrGr($psp);
  my $pb=getPrBer($psp);
  my $ein=fixNumber($l[7]);
  my $aus=fixNumber($l[8]);
  $hash->{$psp}{"type"}="PSP";
  $hash->{$psp}{"ein"}+=$ein;
  $hash->{$psp}{"aus"}+=$aus;
  $hash->{$ssp}{"type"}="Bezug";
  $hash->{$ssp}{"ein"}+=$ein;
  $hash->{$ssp}{"aus"}+=$aus;
  $hash->{$pugr}{"type"}="PrUGr";
  $hash->{$pugr}{"ein"}+=$ein;
  $hash->{$pugr}{"aus"}+=$aus;
  $hash->{$pgr}{"type"}="PrGr";
  $hash->{$pgr}{"ein"}+=$ein;
  $hash->{$pgr}{"aus"}+=$aus;
  $hash->{$pb}{"type"}="PrBer";
  $hash->{$pb}{"ein"}+=$ein;
  $hash->{$pb}{"aus"}+=$aus;
  $hash->{"Top"}{"type"}="";
  $hash->{"Top"}{"ein"}+=$ein;
  $hash->{"Top"}{"aus"}+=$aus;
}

sub processLine {
# bezieht sich auf Datei ErgHH*
  local $_=shift;
  my @l=split /\|/;
  return check($_) if $l[11] eq "KA_B_512"; 
  return check($_) if $l[11] eq "KA_B_513"; 
  return check($_) if $l[11] eq "KA_B_522"; 
  return check($_) if $l[11] eq "KA_B_610"; 
  return check($_) if $l[11] eq "KA_B_620"; 
  my $psp=getPSP($l[1]);
  my $ssp=getSSP($psp);
  my $pugr=getPrUGr($psp);
  my $pgr=getPrGr($psp);
  my $pb=getPrBer($psp);
  my $ein=fixNumber($l[7]);
  my $aus=fixNumber($l[6]);
  $hash->{$psp}{"type"}="PSP";
  $hash->{$psp}{"ein"}+=$ein;
  $hash->{$psp}{"aus"}+=$aus;
  $hash->{$ssp}{"type"}="Bezug";
  $hash->{$ssp}{"ein"}+=$ein;
  $hash->{$ssp}{"aus"}+=$aus;
  $hash->{$pugr}{"type"}="PrUGr";
  $hash->{$pugr}{"ein"}+=$ein;
  $hash->{$pugr}{"aus"}+=$aus;
  $hash->{$pgr}{"type"}="PrGr";
  $hash->{$pgr}{"ein"}+=$ein;
  $hash->{$pgr}{"aus"}+=$aus;
  $hash->{$pb}{"type"}="PrBer";
  $hash->{$pb}{"ein"}+=$ein;
  $hash->{$pb}{"aus"}+=$aus;
  $hash->{"Top"}{"type"}="";
  $hash->{"Top"}{"ein"}+=$ein;
  $hash->{"Top"}{"aus"}+=$aus;
}

sub check {
  local $_=shift;
  my @l=split /\|/;
  my $ein=($l[7]);
  my $aus=($l[6]);
  print "$l[11]|$l[1]|$ein|$aus\n";
}

sub createOutput {
  my $out;
  map {
    my $id=$_;
    my $ein=$hash->{$_}{'ein'};
    my $aus=$hash->{$_}{'aus'}; 
    my $type=$hash->{$_}{'type'}; 
    $out.=<<EOT;
ihrdata:$basis-$type$id
    ihr:relatesTo ihr:$type$id ;
    ihr:jahr "$jahr" ;
    ihr:kategorie ihr:Ergebnishaushalt ;
    ihr:ein "$ein"; ihr:aus "$aus"; 
    ihr:waehrung dbpedia:Euro ;
    qb:dataSet ihrds:$basis ;
    a qb:Observation .

EOT
  } (sort keys %$hash);
  return $out;
}

sub getPSP { 
  local $_=shift;
  s/\.//g;
  return $_;
}

sub getSSP { 
  local $_=shift;
  return substr($_,0,10);
}

sub getPrUGr { 
  local $_=shift;
  return substr($_,4,4);
}

sub getPrGr { 
  local $_=shift;
  return substr($_,4,3);
}

sub getPrBer { 
  local $_=shift;
  return substr($_,4,2);
}

sub OntoPrefix() {
  return '
<http://haushaltsrechner.leipzig.de/Data/'.$basis.'/>
    dct:contributor "Hans-Gert Gräbe" ;
    dct:creator "Projekt Interaktiver Haushaltsrechner für Leipzig 2015" ;
    a owl:Ontology ;
    rdfs:comment "Quelle: HH2015-2/'.$basis.'.csv" ;
    rdfs:label "RDF Cube Haushaltsdaten 2015/16. Ergebnishaushalt '.$jahr.'."@de .

#
# DataSet
# 
ihrds:'.$basis.' a qb:DataSet ;
        rdfs:label "Haushaltsdaten 2015 der Stadt Leipzig. Ergebnishaushalt Planansatz '.$jahr.'."@de ;
        rdfs:comment "Daten zum Haushalt 2015/16 der Stadt Leipzig"@de ;
        dct:source "Kämmerei der Stadtverwaltung Leipzig" ;
        qb:structure ihr:DSDShort .

';
}
