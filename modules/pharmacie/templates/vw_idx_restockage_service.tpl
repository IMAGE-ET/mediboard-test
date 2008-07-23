{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["service_id", "_date_min", "_date_max", "received"];
  filter = new Filter("filter-reception", "{{$m}}", "httpreq_vw_restockages_service_list", "list-deliveries", filterFields);
  filter.submit();
});

function refreshRestockagesList() {
  url = new Url;
  url.setModuleAction("pharmacie", "httpreq_vw_restockages_service_list");
  url.requestUpdate("list-deliveries", { waitingText: null } );
}
</script>

<form name="filter-reception" action="?" method="post" onsubmit="return ">
  <input type="hidden" name="m" value="{{$m}}" />
  <table class="form">
    <tr>
      <th>{{mb_title object=$delivrance field=_date_min}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_min form=filter-reception register=1}}</td>
      <th>{{mb_title object=$delivrance field=_date_max}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_max form=filter-reception register=1}}</td>
      <td>
        <select name="service_id">
        {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>
      </td>
      <td>
        <input name="received" type="radio" value="false" checked="checked" /> non reçues</label>
        <input name="received" type="radio" value="true" /> reçues</label>
      </td>
      <td><button type="button" class="search" onclick="filter.submit();">{{tr}}Filter{{/tr}}</button></td>
    </tr>
  </table>
</form>

<div id="list-deliveries"></div>