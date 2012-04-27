<script type="text/javascript">

function popupImport() {
  var url = new Url("dPhospi", "lits_import_csv");
  url.popup(800, 600, "Import des Lits");
  return false;
}

Main.add(function () {
  PairEffect.initGroup("serviceEffect");
  Control.Tabs.create('tabs-chambres', true);
});

function showInfrastructure(type_id, valeur_id, update_name){
  var url = new Url("dPhospi", "inc_vw_infrastructure");
  url.addParam(type_id, valeur_id);
  url.requestUpdate(update_name);
}
function showLit(type_id, valeur_id, type_id2, valeur_id2, update_name){
  var url = new Url("dPhospi", "inc_vw_infrastructure");
  url.addParam(type_id, valeur_id);
  url.addParam(type_id2, valeur_id2);
  url.requestUpdate(update_name);
}
function submit_Ajax(form, update_name){
  return onSubmitFormAjax(form, {
      onComplete : function() {
        var url = new Url("dPhospi", "ajax_list_infrastructure");
        url.addParam("type_name", update_name);
        url.requestUpdate(update_name);
      }
    });
}
</script>

{{mb_script module=hospi script=affectation_uf}}

<ul id="tabs-chambres" class="control_tabs">
  <li><a href="#secteurs">{{tr}}CSecteur{{/tr}}</a></li>
  <li><a href="#services">{{tr}}CService{{/tr}}</a></li>
  <li><a href="#chambres">{{tr}}CChambre{{/tr}}</a></li>
  <li><a href="#UF">{{tr}}CUniteFonctionnelle{{/tr}}</a></li>
  <li><a href="#prestations">{{tr}}CPrestation{{/tr}}</a></li>
  <li><button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button></li>
</ul>
<hr class="control_tabs" />

<div id="secteurs" style="display: none;">
  {{include file="inc_vw_idx_secteurs.tpl"}}
</div>

<div id="services" style="display: none;">
  {{include file="inc_vw_idx_services.tpl"}}
</div>

<div id="chambres" style="display: none;">
  {{include file="inc_vw_idx_chambres.tpl"}}
</div>

<div id="UF" style="display: none;">
  {{include file="inc_vw_idx_ufs.tpl"}}
</div>

<div id="prestations" style="display: none;">
  {{include file="inc_vw_idx_prestations.tpl"}}
</div>