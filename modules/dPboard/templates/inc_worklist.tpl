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
  return false;
}

updateNbActes = function() {
  return false;
}

Main.add(function () {
  tabs = new Control.Tabs('tab-worklist');
  updatePrescriptions();
  updateActes();
});

</script>


<ul id="tab-worklist" class="control_tabs">
  <li><a href="#prescriptions_non_signees">Prescriptions <span id=nb_prescriptions></span></a></li>
  <li style="display: none;"><a href="#actes_non_cotes">Actes</a></li>
</ul>

<div id="prescriptions_non_signees">
</div>

<div id="actes_non_cotes">
</div>