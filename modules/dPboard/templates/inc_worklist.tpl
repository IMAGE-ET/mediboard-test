<script>

initUpdatePrescriptions = function() {
  var url = new Url("soins", "httpreq_vw_bilan_list_prescriptions");
  url.addParam("prat_bilan_id" , "{{$chirSel}}");
  url.addParam("_date_entree_prevue", "{{$date}}");
  url.addParam("_date_sortie_prevue", "{{$date}}");
  url.addParam("board" , "1");
  url.periodicalUpdate("prescriptions_non_signees", { frequency: 120 } );
}

updatePrescriptions = function() {
  var url = new Url("soins", "httpreq_vw_bilan_list_prescriptions");
  url.addParam("prat_bilan_id" , "{{$chirSel}}");
  url.addParam("_date_entree_prevue", "{{$date}}");
  url.addParam("_date_sortie_prevue", "{{$date}}");
  url.addParam("board" , "1");
  url.requestUpdate("prescriptions_non_signees");
}

updateNbPrescriptions = function(nb) {
  $('nb_prescriptions').update('('+nb+')');
}

initUpdateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id", "{{$chirSel}}");
  url.addParam("fin", "{{$date}}");
  url.addParam("board"       , "1");
  url.periodicalUpdate("actes_non_cotes", { frequency: 120 } );
}

updateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id", "{{$chirSel}}");
  url.addParam("fin", "{{$date}}");
  url.addParam("board"       , "1");
  url.requestUpdate("actes_non_cotes");
}

updateNbActes = function(nb) {
  $('nb_actes').update('('+nb+')');
}

Main.add(function () {
  var tabs = Control.Tabs.create('tab-worklist', true);
  {{if "dPprescription"|module_active}}
  initUpdatePrescriptions();
  {{/if}}
  initUpdateActes();
});

</script>


<ul id="tab-worklist" class="control_tabs">
  {{if "dPprescription"|module_active}}
  <li><a href="#prescriptions_non_signees">Prescriptions <span id="nb_prescriptions"></span></a></li>
  {{/if}}
  <li><a href="#actes_non_cotes">Actes <span id="nb_actes"></span></a></li>
</ul>

{{if "dPprescription"|module_active}}
<div id="prescriptions_non_signees">
</div>
{{/if}}

<div id="actes_non_cotes">
</div>