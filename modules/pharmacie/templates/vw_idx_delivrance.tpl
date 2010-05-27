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
  urlGlobales.requestUpdate("list-globales");

  urlNominatives.addParam("mode", "nominatif");
  urlNominatives.requestUpdate("list-nominatives");

  return false;
}

function refreshDeliveryLine(delivery_id) {
  var form = getForm("filter");
  var url = new Url("pharmacie", "httpreq_vw_delivery");
  url.addParam("delivery_id", delivery_id);
  url.addParam("date_min", $V(form.date_min));
  url.addParam("date_max", $V(form.date_max));
  url.requestUpdate("CProductDelivery-"+delivery_id);
}

function printPreparePlan(form) {
  var url = new Url("pharmacie", "print_prepare_plan");
  url.addFormData(form);
  url.pop(800, 600, 'Plan de cueillette');
  return false;
}

function deliverLine(form, dontRefresh, refreshAll) {
  return onSubmitFormAjax(form, {
    onComplete: 
      dontRefresh ? Prototype.emptyFunction : 
        refreshAll ? refreshLists : refreshDeliveryLine.curry($V(form.delivery_id) || $V(form._delivery_id))
  });
}

function deliverAll(container) {
  var i, listForms = [];
  $(container).select("form").each(function(f) {
    if ((!f.del || $V(f.del) == "0") && $V(f.delivery_id) && $V(f.date_delivery) == 'now' && parseInt($V(f.quantity)) > 0) {
    	listForms.push(f);
    }
  });

  for (i = 0; i < listForms.length; i++) {
	  deliverLine(listForms[i], i != listForms.length-1, true);
  }
}
</script>

<script type="text/javascript">
var tabs;
Main.add(function () {
  tabs = Control.Tabs.create('tab_delivrances', true);
  refreshLists();
});
</script>

<form name="filter" action="?" method="get" onsubmit="if(window.loadSuivi) loadSuivi($V(this.sejour_id)); return (checkForm(this) && refreshLists())">
  <input type="hidden" name="m" value="{{$m}}" />
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_date_min form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form=filter register=1 onchange="this.form.onsubmit()"}}</td>
      <td>
        <label>
          <input type="checkbox" name="display_delivered" onclick="this.form.onsubmit()" /> Afficher les délivrances completées
        </label>
      </td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<ul id="tab_delivrances" class="control_tabs">
  <li><a href="#list-globales">Globales <small>(0)</small></a></li>
  <li><a href="#list-nominatives">Nominatives <small>(0)</small></a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-globales" style="display: none;"></div>
<div id="list-nominatives" style="display: none;"></div>
