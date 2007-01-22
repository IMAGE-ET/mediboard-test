<script type="text/javascript">
var oResponse = {
  "oEtablissements"  : {{$etablissements|@json}},
  "oServices"        : {{$services|@json}}
};
AjaxResponse.putServices("services",oResponse);
</script>