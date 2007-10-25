<script type="text/javascript">
oResponse = {
  "oEtablissements"  : {{$etablissements|@json}},
  "oServices"        : {{$services|@json}}
};
AjaxResponse.putServices("services",oResponse);
</script>