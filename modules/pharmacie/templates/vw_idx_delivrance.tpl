{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["service_id"];
  filter = new Filter("filter-delivrance", "{{$m}}", "httpreq_vw_deliveries_list", "list-deliveries", filterFields);
  filter.submit();
});

function refreshDeliveriesList() {
  url = new Url;
  url.setModuleAction("pharmacie","httpreq_vw_deliveries_list");
  url.requestUpdate("list-deliveries", { waitingText: null } );
}
</script>

<form name="filter-delivrance" action="?" method="post" onsubmit="return filter.submit('keywords');">
  <input type="hidden" name="m" value="{{$m}}" />
  <table class="form">
    <tr>
      <th>{{mb_title object=$delivrance field=_date_min}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_min form=filter-delivrance register=1}}</td>
      <th>{{mb_title object=$delivrance field=_date_max}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_max form=filter-delivrance register=1}}</td>
      <td>
        <select name="service_id" onchange="filter.submit();">
        {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
  
        <button type="button" class="search" onclick="filter.submit();">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="filter.empty();">{{tr}}Reset{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<div id="list-deliveries"></div>