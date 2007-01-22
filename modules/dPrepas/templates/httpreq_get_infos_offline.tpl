<script type="text/javascript">
var oResponse = {
  "oAffectations"  : {{$aAffectation|@json}},
  "oSejours"       : {{$aSejours|@json}},
  "oPatients"      : {{$aPatients|@json}},
  "oListTypeRepas" : {{$listTypeRepas|@json}},
  "oMenus"         : {{$aMenus|@json}},
  "oRepas"         : {{$aRepas|@json}},
  "oPlats"         : {{$aPlats|@json}},
  "oPlanningRepas" : {{$planningRepas|@json}},
  "config"         : {{$dPrepas|@json}}
};
AjaxResponse.putdPrepasData("dPrepas",oResponse);
</script>