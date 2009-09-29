{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function refreshLists() {
  var form = getForm("filter");
  var urlGlobales = new Url("pharmacie", "httpreq_vw_deliveries_list");
  var urlNominatives = new Url("pharmacie", "httpreq_vw_deliveries_list");

  $A(form.elements).each (function (e) {
    urlGlobales.addParam(e.name, $V(e));
    urlNominatives.addParam(e.name, $V(e));
  });

  // To choose wether we want global or nominative deliveries
  urlGlobales.addParam("mode", "global");
  urlGlobales.requestUpdate("list-globales", { waitingText: null } );

  urlNominatives.addParam("mode", "nominatif");
  urlNominatives.requestUpdate("list-nominatives", { waitingText: null } );

  return false;
}

function printPreparePlan(nominatif) {
  var form = getForm("filter");
  var url = new Url;
  
  if (nominatif) {
    url.setModuleAction("dPhospi", "vw_bilan_service");
    url.addParam("token_cat", "med");
    url.addParam("do", 1);
    url.addParam("_dateTime_min", $V(form._date_min)+' 00:00:00');
    url.addParam("_dateTime_max", $V(form._date_max)+' 23:59:59');
    url.addParam("service_id", $V(form.service_id));
    url.addParam("hide_filters", 1);
  }
  else {
    url.setModuleAction("pharmacie", "print_prepare_plan");
    url.addObjectParam(form.serialize());
  }
  
  url.pop(800, 600, 'Plan de cueillette');
}

function deliverLine(form, dontRefresh) {
  return onSubmitFormAjax(form, {onComplete: dontRefresh ? Prototype.emptyFunction : refreshLists});
}

function deliverAll(container) {
  var i, listForms = [];
  $(container).select("form").each(function(f) {
    if ((!f.del || $V(f.del) == "0") && $V(f.delivery_id) && $V(f.date_delivery) == 'now' && parseInt($V(f.quantity)) > 0) {
    	listForms.push(f);
    }
  });

  for (i = 0; i < listForms.length; i++) {
	  deliverLine(listForms[i], i != listForms.length-1);
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