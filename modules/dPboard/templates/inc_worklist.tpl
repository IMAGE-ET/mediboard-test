<script>

updatePrescriptions = function() {
  var url = new Url("soins", "httpreq_vw_bilan_list_prescriptions");
  url.addParam("prat_bilan_id" , "{{$chirSel}}");
  url.addParam("board" , "1");
  url.requestUpdate("prescriptions_non_signees");
}

updateNbPrescriptions = function(nb) {
  $('nb_prescriptions').update('('+nb+')');
}

updateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id", "{{$chirSel}}");
  url.addParam("board"       , "1");
  url.requestUpdate("actes_non_cotes");
}

updateNbActes = function() {
  return false;
}

Main.add(function () {
  var tabs = Control.Tabs.create('tab-worklist', true);
  {{if "dPprescription"|module_active}}
  updatePrescriptions();
  {{/if}}
  updateActes();
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