{{mb_script module="dPplanningOp" script="cim10_selector"}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
{{/if}}

<script type="text/javascript">
Main.add(function(){
  var url = new Url("dPcabinet", "httpreq_vw_antecedents");
  url.addParam("sejour_id", '{{$object->_id}}');
  url.addParam("show_header", 0);
  url.requestUpdate('tab-native_views-atcd');
});
</script>