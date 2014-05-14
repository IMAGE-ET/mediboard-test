<script type="text/javascript">
refreshConstantesMedicales = function(context_guid) {
  var url = new Url("patients", "httpreq_vw_constantes_medicales");
  url.addParam("context_guid", context_guid);

  {{assign var=rel_patient value=$object->loadRelPatient()}}
  url.addParam("patient_id", '{{$rel_patient->_id}}');

  url.addParam("readonly", '0');
  //url.addParam("selected_context_guid", context_guid);
  url.addParam("paginate", window.paginate || 0);
  url.addParam("simple_view", 1);
  if (window.oGraphs) {
    url.addParam('hidden_graphs', JSON.stringify(window.oGraphs.getHiddenGraphs()));
  }
  url.requestUpdate("tab-native_views-constantes", function(){
    loadConstantesMedicales = refreshConstantesMedicales; // FIXME
  });
};

ExObject.groupTabsCallback["tab-native_views-constantes"] = function(){
  refreshConstantesMedicales('{{$object->_guid}}');
};
</script>