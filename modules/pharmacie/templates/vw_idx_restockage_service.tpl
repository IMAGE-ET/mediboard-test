<script type="text/javascript">
function refreshLists() {
  var form = getForm("filter");
  
  urlGlobales = new Url;
  urlGlobales.setModuleAction("pharmacie", "httpreq_vw_restockages_service_list");
  
  urlNominatives = new Url;
  urlNominatives.setModuleAction("pharmacie", "httpreq_vw_restockages_service_list");

  $A(form.elements).each (function (e) {
    urlGlobales.addParam(e.name, $V(e));
    urlNominatives.addParam(e.name, $V(e));
  });

  // To choose wether we want global or nominative deliveries
  urlGlobales.addParam("mode", "global");
  urlNominatives.addParam("mode", "nominatif");
  
  urlGlobales.requestUpdate("list-globales", { waitingText: null } );
  urlNominatives.requestUpdate("list-nominatives", { waitingText: null } );

  return false;
}

function receiveLine(form, dontRefresh) {
  return onSubmitFormAjax(form, {onComplete: dontRefresh ? Prototype.emptyFunction : refreshLists});
}

function receiveAll(container) {
  var listForms = [];
  $(container).select("form").each(function(f) {
    if ((!f.del || $V(f.del) == "0") && $V(f.delivery_trace_id) && $V(f.date_reception) == 'now') {
      listForms.push(f);
    }
  });

  for (i = 0; i < listForms.length; i++) {
    receiveLine(listForms[i], i != listForms.length-1);
  }
}
</script>

{{include file=inc_filter_delivrances.tpl}}

<ul id="tab_delivrances" class="control_tabs">
  <li><a href="#list-globales">Globales <small>(0)</small></a></li>
  <li><a href="#list-nominatives">Nominatives <small>(0)</small></a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-globales" style="display: none;"></div>
<div id="list-nominatives" style="display: none;"></div>
