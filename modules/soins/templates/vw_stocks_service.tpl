{{* $Id: vw_idx_restockage_service.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
function refreshLists() {
  var url, form = getForm("filter");
    
  refreshOrders();
  
  url = new Url("soins", "httpreq_vw_stock_reception");
  url.addFormData(form);
  url.requestUpdate("list-reception", { waitingText: null } );
  
  url = new Url("soins", "httpreq_vw_stock_inventory");
  url.addFormData(form);
  url.requestUpdate("list-inventory", { waitingText: null } );
  
  return false;
}

function refreshOrders(){
  var url = new Url("soins", "httpreq_vw_stock_order");
  url.addFormData(getForm("filter"));
  url.requestUpdate("list-order", { waitingText: null } );
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

var tabs;
Main.add(function () {
  refreshLists();
  tabs = Control.Tabs.create('tab_stocks_soins', true);
});

</script>

<form name="filter" action="?" method="get" onsubmit="return (checkForm(this) && refreshLists())">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="start" value="{{$start}}" />
  <input type="hidden" name="only_service_stocks" value="{{$only_service_stocks}}" onchange="$V(this.form.start, 0); refreshOrders()"/>
  <input type="hidden" name="only_common" value="{{$only_common}}" onchange="$V(this.form.start, 0); refreshOrders()"/>
  <table class="form">
    <tr>
      <th>{{mb_label object=$delivrance field=_date_min}}</th>
      <td>{{mb_field object=$delivrance field=_date_min form=filter register=true onchange="\$V(this.form.start, 0); refreshOrders()"}}</td>
      <th>{{mb_label object=$delivrance field=_date_max}}</th>
      <td>{{mb_field object=$delivrance field=_date_max form=filter register=true onchange="\$V(this.form.start, 0); refreshOrders()"}}</td>
      <td>
        <select name="service_id" onchange="$V(this.form.start, 0); refreshOrders()">
        {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
      </td>
      <td><button class="search">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<ul id="tab_stocks_soins" class="control_tabs">
  <li><a href="#list-order">Commandes</a></li>
  <li><a href="#list-reception">Réceptions <small>(0)</small></a></li>
  <li><a href="#list-inventory">Inventaire</a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-order" style="display: none;"></div>
<div id="list-reception" style="display: none;"></div>
<div id="list-inventory" style="display: none;"></div>
