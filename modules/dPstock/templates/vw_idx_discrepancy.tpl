{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  // Menu tabs initialization
  var tabs = Control.Tabs.create('tab_discrepancies', true);
  
  filterFields = ["service_id",  "all_stocks"];
  filter = new Filter("list-filter", "{{$m}}", "httpreq_vw_discrepancies_list", "list-stocks-service", filterFields);
  filter.submit();
  
  url = new Url;
  url.setModuleAction("dPstock", "httpreq_vw_stocks_group_list");
  //url.addParam("category_id", category_id);
  url.requestUpdate("list-stocks-group", { waitingText: null } );
});
</script>

<!-- Filter -->
<form name="list-filter" action="?" method="post" onsubmit="return filter.submit();">
  <input type="hidden" class="m" name="{{$m}}" />
  <select name="service_id" style="margin:0" onchange="filter.submit()">
  {{foreach from=$list_services item=curr_service}}
    <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
  {{/foreach}}
  </select>
</form>

<!-- Tabs titles -->
<ul id="tab_discrepancies" class="control_tabs">
  <li><a href="#list-stocks-group">{{tr}}CProductStockGroup{{/tr}}</a></li>
  <li><a href="#list-stocks-service">{{tr}}CProductStockService{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-stocks-group" style="display: none;"></div>
<div id="list-stocks-service" style="display: none;"></div>