<table class="form">
  <col style="width: 50%" />
  <tr>
    <th class="category" colspan="2">
      {{tr}}Offline{{/tr}}
    </th>
  </tr>
  <tr>
    <td style="text-align: right">
      {{tr}}CChambre-service_id{{/tr}} :
      <select id="service_id">
        {{foreach from=$services item=_service}}
          <option value='{{$_service->_id}}'>{{$_service->nom}}</option>
        {{/foreach}}
      </select>
    </td>
    <td>
      <a class="button search" onclick="redirectOffline('sejour');">{{tr}}CSejour.all{{/tr}}</a>
      <a class="button download" onclick="redirectOffline('sejour', true);">{{tr}}Download{{/tr}} {{tr}}CSejour.all{{/tr}}</a>
      {{if "dPprescription"|module_active}}
        <br/>
        <a class="button search" onclick="redirectOffline('bilan');">{{tr}}CService.bilan{{/tr}}</a>
        <br />
        <a class="button search" onclick="redirectOffline('plan');">{{tr}}CService.plan_soins{{/tr}}</a>
      {{/if}}
    </td>
  </tr>
</table>