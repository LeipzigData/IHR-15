# parse Finanzpositionengruppen.csv

use Helper;

undef $/;
open(FH,shift) or die;
$_=<FH>;
close FH;
my $out;
map { $out.=processFP($_); } split /\n/;
#print $out;
print TurtlePrefix().$out;

### end main

sub processFP {
  local $_=shift;
  my @l=split /\s*\|\s*/; 
  my $grp=$l[1];
  my $id=$l[2];
  my $bezeichnung=$l[3]; 
  my $von=$l[4];
  my $bis=$l[5];
  return <<EOT;
ihr:$id
    ihr:BezeichnungStadt "$bezeichnung" ;
    ihr:hatStadtId "$id" ;
    ihr:zurGruppe "$grp" ;
    ihr:Positionen "$von .. $bis" ;
    a ihr:Finanzposition ;
    rdfs:label "$bezeichnung"\@de .

EOT
}
