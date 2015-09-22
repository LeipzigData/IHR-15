# parse FinHH_lfdVw_2015.csv

use Helper;

undef $/;
open(FH,shift) or die;
$_=<FH>;
close FH;

my $cnt=1000;
my $out;
#map { $out.=getDescription($_); } split /\n/;
map { $out.=getEinordnung($_); } split /\n/;
#print $out;
print TurtlePrefix().$out;

### end main

sub getDescription {
# Extrahiere die Finanzpositionen und deren Einordnung
# Konsolidiert in FinanzHaushaltLeipzigModel.ttl
  local $_=shift;
  my @l=split /\s*\|\s*/; 
  my $fp=$l[11];
  my $fptext=$l[12];
  my $fpgr=$l[13];
  my $fpgrtext=$l[14];
  return <<EOT;
ihr:F$fp 
    ihr:BezeichnungStadt "$fptext" ;
    ihr:hatStadtId "$fp" ;
    ihr:zurGruppe ihr:$fpgr ;
    ihr:belongsTo ihr:InvFinHH ;
    a ihr:Finanzposition ;
    rdfs:label "$fptext"\@de .

ihr:$fpgr
    ihr:BezeichnungStadt "$fpgrtext" ;
    ihr:hatStadtId "$fpgr" ;
    ihr:belongsTo ihr:InvFinHH ;
    a ihr:FPGruppe ;
    rdfs:label "$fpgrtext"\@de .


EOT
}

sub getEinordnung {
# Extrahiere die Zuordnung einer Finanzposition zu PrUgr
# Konsolidiert in FinanzHaushaltLeipzigModel.ttl
  local $_=shift;
  my @l=split /\s*\|\s*/; 
  my $prugr=$l[4];
  my $fp=$l[11];
  return "ihr:PrUGr$prugr ihr:hasChild ihr:F$fp .\n" if $prugr;
  return "";
}
