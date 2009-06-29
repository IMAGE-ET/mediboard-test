<script type="text/javascript">

refreshConstantesMedicales = function(context_guid) {
  console.debug(context_guid);
  if(context_guid) {
    var url = new Url;
    url.setModuleAction("dPhospi", "httpreq_vw_constantes_medicales");
    url.addParam("context_guid", context_guid);
    url.requestUpdate("constantes", { waitingText: null } );
  }
}

constantesMedicalesDrawn = false;
refreshConstantesHack = function(sejour_id) {
  console.debug(sejour_id);
  if (constantesMedicalesDrawn == false && $('constantes').visible() && sejour_id) {
    refreshConstantesMedicales('CSejour-'+sejour_id);
    constantesMedicalesDrawn = true;
  }
}

loadResultLabo = function(sejour_id) {
  var url = new Url("dPImeds", "httpreq_vw_sejour_results");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('result_labo', { waitingText : null });
}


Main.add( function(){
  dossier_sejour_tabs = Control.Tabs.create('dossier_sejour_tab_group', false);
  refreshConstantesHack('{{$object->_id}}');
  loadResultLabo('{{$object->_id}}');
} );

</script>

<ul id="dossier_sejour_tab_group" class="control_tabs">
  <li><a href="#div_sejour">Séjour</a></li>
  <li onclick="refreshConstantesHack('{{$object->_id}}')"><a href="#constantes">Constantes</a></li>
  <li><a href="#result_labo">Labo</a></li>
</ul>

<hr class="control_tabs" />

<div id="div_sejour" style="display:none;">
  {{include file="../../dPplanningOp/templates/CSejour_complete.tpl"}}
</div>

<div id="constantes" style="display:none;">

</div>

<div id="result_labo" style="display:none;">

</div>