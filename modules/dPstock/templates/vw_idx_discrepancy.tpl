{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  // Menu tabs initialization
  var tabs = Control.Tabs.create('tab_discrepancies', true);
  
  filterServiceFields = ["service_id", "category_id", "keywords", "limit"];
  filterService = new Filter("list-filter-service", "{{$m}}", "httpreq_vw_stocks_service_list", "list-stocks-service", filterServiceFields);
  filterService.submit();
  
  filterGroupFields = ["category_id", "keywords", "limit"];
  filterGroup = new Filter("list-filter-group", "{{$m}}", "httpreq_vw_stocks_group_list", "list-stocks-group", filterServiceFields);
  filterGroup.submit();
});
</script>

<!-- Tabs titles -->
<ul id="tab_discrepancies" class="control_tabs">
  <li><a href="#list-stocks-group-filter">{{tr}}CProductStockGroup{{/tr}}</a></li>
  <li><a href="#list-stocks-service-filter">{{tr}}CProductStockService{{/tr}}</a></li>
</ul>
<hr class="control_tabs" />

<!-- Tabs containers -->
<div id="list-stocks-group-filter" style="display: none;">
  <form name="list-filter-group" action="?" method="post" onsubmit="return filterGroup.submit()">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="limit" value="" />
    <input type="text" name="keywords" value="" />
    <select name="category_id" onchange="filterGroup.submit();">
      <option value="0">&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
      {{foreach from=$list_categories item=curr_category}}
      <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
      {{/foreach}}
    </select>
  </form>
  
  <div id="list-stocks-group"></div>
</div>

<div id="list-stocks-service-filter" style="display: none;">
  <form name="list-filter-service" action="?" method="post" onsubmit="return filterService.submit()">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="limit" value="" />
    <input type="text" name="keywords" value="" />
    <select name="category_id" onchange="filterService.submit();">
      <option value="0">&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
      {{foreach from=$list_categories item=curr_category}}
      <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
      {{/foreach}}
    </select>
    <select name="service_id" onchange="filterService.submit()">
    {{foreach from=$list_services item=curr_service}}
      <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
    {{/foreach}}
    </select>
  </form>
  
  <div id="list-stocks-service"></div>
</div>