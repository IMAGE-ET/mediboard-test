<script type="text/javascript">
refreshConstantesMedicales = function(context_guid) {
  var url = new Url("dPhospi", "httpreq_vw_constantes_medicales");
  url.addParam("context_guid", context_guid);
  url.addParam("patient_id", '{{$object->loadRelPatient()->_id}}');
  url.addParam("readonly", '0');
  //url.addParam("selected_context_guid", context_guid);
  url.addParam("paginate", window.paginate || 0);
  url.addParam("simple_view", 1);
  url.requestUpdate("tab-native_views-constantes", function(){
    loadConstantesMedicales = refreshConstantesMedicales; // FIXME
  });
}

Main.add(function(){
  $$("a[href=#tab-native_views-constantes]")[0].observeOnce("mousedown", function(){
    refreshConstantesMedicales('{{$object->_guid}}');
  });
});
</script>