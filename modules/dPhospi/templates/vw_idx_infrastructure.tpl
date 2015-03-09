<script>
function popupImport() {
  var url = new Url("dPhospi", "lits_import_csv");
  url.popup(800, 600, "Import des Lits");
  return false;
}

Main.add(function () {
  Control.Tabs.create('tabs-chambres', true);
});

function submit_Ajax(form, update_name){
  return onSubmitFormAjax(form, {
    onComplete : function() {
      var url = new Url("dPhospi", "ajax_list_infrastructure");
      url.addParam("type_name", update_name);
      url.requestUpdate(update_name);
    }
  });
}

function viewStatUf(uf_id){
  var url = new Url("hospi", "vw_stats_uf");
  url.addParam("uf_id", uf_id);
  url.requestModal(400, 300);
}
</script>
{{mb_script module=hospi script=infrastructure ajax=true}}
{{mb_script module=hospi script=affectation_uf}}

<ul id="tabs-chambres" class="control_tabs">
  <li><a href="#secteurs">{{tr}}CSecteur{{/tr}} {{if $secteurs|@count}}({{$secteurs|@count}}){{/if}}</a></li>
  <li><a href="#services">{{tr}}CService{{/tr}} {{if $services|@count}}({{$services|@count}}){{/if}}</a></li>
  <li><a href="#UF">{{tr}}CUniteFonctionnelle{{/tr}}</a></li>
  <li><button type="button" style="float:right;" onclick="return popupImport();" class="hslip">{{tr}}Import-CSV{{/tr}}</button></li>
</ul>
<hr class="control_tabs" />

<div id="secteurs" style="display: none;">
  {{mb_include template="inc_vw_idx_secteurs"}}
</div>

<div id="services" style="display: none;">
  {{mb_include template="inc_vw_idx_services"}}
</div>

<div id="UF" style="display: none;">
  {{mb_include template="inc_vw_idx_ufs"}}
</div>