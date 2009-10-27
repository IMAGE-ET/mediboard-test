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
  url.setModuleAction("pharmacie", "print_prepare_plan");
  url.addObjectParam(form.serialize());
  url.addParam("nominatif", nominatif);
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
      <td>{{mb_field object=$delivrance field=_date_min form=filter register=1}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form=filter register=1}}</td>
      <td>
        <select name="service_id" onchange="this.form.onsubmit();">
        {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
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