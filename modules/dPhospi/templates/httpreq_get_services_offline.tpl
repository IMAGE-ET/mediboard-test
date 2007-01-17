<script type="text/javascript">
var oResponse = {
  "oEtablissements"  : {{$etablissements|@json}},
  "oServices"        : {{$aListServices|@json}}
};
AjaxResponse.storeData("services",oResponse);
</script>