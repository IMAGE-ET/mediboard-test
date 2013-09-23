<script type="text/javascript">
showActesNGAP = function(sejour_id){
  var url = new Url("dPcabinet", "httpreq_vw_actes_ngap");
  url.addParam("object_id"   , sejour_id);
  url.addParam("object_class", "CSejour");
  url.requestUpdate('listActesNGAP');
};

showFraisDivers = function(sejour_id) {
  var url = new Url("dPurgences", "ajax_show_frais_divers");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('fraisdivers');
};

Main.add(function () {
  var tab_actes = Control.Tabs.create('tab-actes', false);

  showActesNGAP('{{$sejour->_id}}');
});
</script>

<ul id="tab-actes" class="control_tabs">
  <li><a href="#listActesNGAP">Actes NGAP</a></li>
  {{if $conf.dPccam.CCodable.use_frais_divers.CSejour}}
    <li onmouseup="showFraisDivers('{{$sejour->_id}}')"><a href="#fraisdivers">Frais divers</a></li>
  {{/if}}
</ul>

<hr class="control_tabs" />

<div id="listActesNGAP"></div>

{{if $conf.dPccam.CCodable.use_frais_divers.CSejour}}   
  <div id="fraisdivers" style="display:none"></div>
{{/if}}