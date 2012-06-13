{{mb_script module="bloodSalvage" script="bloodSalvage"}}
{{mb_script module="soins" script="plan_soins"}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="prescription"}}
  {{mb_script module="dPprescription" script="element_selector"}}
{{/if}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{mb_script module="dPplanningOp" script="cim10_selector"}}
{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}
{{mb_script module="dPcabinet" script="file"}}

{{if $isImedsInstalled}}
  {{mb_script module="dPImeds" script="Imeds_results_watcher"}}
{{/if}}

{{if $conf.dPsalleOp.CDailyCheckList.active_salle_reveil != '1' || 
     $date < $smarty.now|iso_date || 
     $check_list->_id && $check_list->validator_id}}
     
<script type="text/javascript">

Main.add(function () {
  new Control.Tabs.create('reveil_tabs', true);
  
  var url = new Url("dPsalleOp", "httpreq_reveil");
  
  url.addParam("bloc_id", "{{$bloc->_id}}");
  url.addParam("date", "{{$date}}");
  
  // Laisser la variable updater_encours, utile dans inc_edit_check_list.tpl
  url.addParam("type", "preop");
  url.periodicalUpdate("preop", { frequency: 90 });  
  
  url.addParam("type", "encours");
  url.periodicalUpdate("encours", { frequency: 90 });  

  url.addParam("type", "ops");
  url.periodicalUpdate("ops", { frequency: 90 });

  url.addParam("type", "reveil");
  url.requestUpdate("reveil");

  url.addParam("type", "out");
  url.requestUpdate("out");
});

function refreshTabsReveil() {
  var url = new Url("dPsalleOp", "httpreq_reveil");
  
  url.addParam("bloc_id", "{{$bloc->_id}}");
  url.addParam("date", "{{$date}}");
  
  url.addParam("type", "preop");
  url.requestUpdate("preop");
  
  url.addParam("type", "encours");
  url.requestUpdate("encours");
  
  url.addParam("type", "ops");
  url.requestUpdate("ops");
  
  url.addParam("type", "reveil");
  url.requestUpdate("reveil");
  
  url.addParam("type", "out");
  url.requestUpdate("out");
}

codageCCAM = function(operation_id){
  var url = new Url("dPsalleOp", "httpreq_codage_actes_reveil");
  url.addParam("operation_id", operation_id);
  url.popup(700,500,"Actes CCAM");
}

showDossierSoins = function(sejour_id, operation_id, default_tab){
  {{if "dPprescription"|module_active}}
  $('dossier_sejour').update("");
  var url = new Url("soins", "ajax_vw_dossier_sejour");
  url.addParam("sejour_id", sejour_id);
  url.addParam("operation_id", operation_id);
  if(default_tab){
    url.addParam("default_tab", default_tab);
  }
	url.requestUpdate($('dossier_sejour'));
  modalWindow = modal($('dossier_sejour'));
	{{/if}}
}

printDossier = function(sejour_id, operation_id) {
  var url = new Url("dPhospi", "httpreq_documents_sejour");
  url.addParam("sejour_id", sejour_id);
  url.addParam("operation_id", operation_id);
  url.requestModal(700, 400);
}

</script>

     
  <ul id="reveil_tabs" class="control_tabs">
    <li><a class="empty" href="#preop"  >{{tr}}SSPI.Preop{{/tr}}   <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#encours">{{tr}}SSPI.Encours{{/tr}} <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#ops"    >{{tr}}SSPI.Attente{{/tr}} <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#reveil" >{{tr}}SSPI.Reveil{{/tr}}  <small>(&ndash;)</small></a></li>
    <li><a class="empty" href="#out"    >{{tr}}SSPI.Sortie{{/tr}}  <small>(&ndash;)</small></a></li>
    
    <li style="float:right; font-weight: bold;">
      {{mb_include template=inc_filter_reveil}}
    </li>
  </ul>
    
  <hr class="control_tabs" />
  
  <div id="preop"   style="display:none"></div>
  <div id="encours" style="display:none"></div>
  <div id="ops"     style="display:none"></div>
  <div id="reveil"  style="display:none"></div>
  <div id="out"     style="display:none"></div>

{{else}}


  <div style="text-align: center">
    {{mb_include template=inc_filter_reveil}}
  </div>
    
  {{include file=inc_edit_check_list.tpl personnel=$personnels}}
{{/if}}

<div id="dossier_sejour" style="width: 95%; height: 90%; overflow: auto; display: none;"></div> 