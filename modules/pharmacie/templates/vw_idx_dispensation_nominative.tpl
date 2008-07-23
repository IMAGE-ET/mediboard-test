{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
 refreshPatient();
 filterFields = ["service_id","_date_min","_date_max","patient_id","prescription_id"];
 filter = new Filter("filter-dispensations", "{{$m}}", "httpreq_vw_dispensations_nominative_list", "list-dispensations", filterFields);
 filter.submit();
});


function refreshPatient(){
  var oForm = document.forms['filter-dispensations'];
  var url = new Url;
  url.setModuleAction("pharmacie","httpreq_vw_list_patients");
  url.addParam("date_min", oForm._date_min.value);
  url.addParam("date_max", oForm._date_max.value);
  url.addParam("service_id", oForm.service_id.value);
  url.requestUpdate("patients", { waitingText: null } );
}

function refreshDeliveriesList() {
  url = new Url;
  url.setModuleAction("pharmacie","httpreq_vw_dispensations_nominative_list");
  url.requestUpdate("list-dispensations", { waitingText: null } );
}
</script>

<form name="filter-dispensations" action="?" method="post" onsubmit="return filter.submit('keywords');">
  <input type="hidden" name="m" value="{{$m}}" />
  <table class="form">
    <tr>
      <th>{{mb_title object=$delivrance field=_date_min}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_min form=filter-dispensations register=1}}</td>
      <th>{{mb_title object=$delivrance field=_date_max}}</th>
      <td class="date">{{mb_field object=$delivrance field=_date_max form=filter-dispensations register=1}}</td>
      <td>
        <select name="service_id" onchange="refreshPatient();">
        {{foreach from=$services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->nom}}</option>
        {{/foreach}}
        </select>  
      </td>
      <td>
        <div id="patients">
        </div>
      </td>
      <td>
        <button type="button" class="search" onclick="filter.submit();">{{tr}}Filter{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<div id="list-dispensations"></div>