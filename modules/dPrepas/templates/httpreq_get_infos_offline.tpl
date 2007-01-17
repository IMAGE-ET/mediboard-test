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
oResponse["oRepas"][0] = {};
AjaxResponse.storeData("dPrepas",oResponse);
</script>