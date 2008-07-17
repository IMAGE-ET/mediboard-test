{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["category_id", "service_id"];
  deliveriesFilter = new Filter("filter-deliveries", "{{$m}}", "httpreq_vw_deliveries_list", "list-deliveries", filterFields);
  deliveriesFilter.submit();
});

function refreshDeliveriesList() {
  url = new Url;
  url.setModuleAction("pharmacie","httpreq_vw_deliveries_list");
  url.requestUpdate("list-deliveries", { waitingText: null } );
}
</script>

<form name="filter-deliveries" action="?" method="post" onsubmit="return deliveriesFilter.submit('keywords');">
  <input type="hidden" name="m" value="{{$m}}" />
  
  <select name="category_id" onchange="deliveriesFilter.submit();">
    <option value="0" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
  {{foreach from=$list_categories item=curr_category}}
    <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
  {{/foreach}}
  </select>
  
  <select name="service_id" onchange="deliveriesFilter.submit();">
    <option value="0" >&mdash; {{tr}}CService.all{{/tr}} &mdash;</option>
  {{foreach from=$list_services item=curr_service}}
    <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
  {{/foreach}}
  </select>
  
  <button type="button" class="search" onclick="deliveriesFilter.submit();">{{tr}}Filter{{/tr}}</button>
  <button type="button" class="cancel notext" onclick="deliveriesFilter.empty();">{{tr}}Reset{{/tr}}</button>
</form>

<div id="list-deliveries"></div>