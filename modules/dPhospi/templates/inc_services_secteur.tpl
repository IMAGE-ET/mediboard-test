<table class="form" id="services_secteur">
  <tr>
    <th class="title" colspan="3">
      {{tr}}CSecteur-back-services{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="category" class="narrow"></th>
    <th class="category">{{mb_label class=CService field=nom}}</th>
    <th class="category text">{{mb_label class=CService field=description}}</th>
  </tr>
  {{foreach from=$secteur->_ref_services item=_service}}
    <tr>
      <td>
        <button type="button" class="cancel notext" onclick="removeService('{{$_service->_id}}')"></button>
      </td>
      <td>
        {{mb_value object=$_service field=nom}}
      </td>
      <td>
        {{mb_value object=$_service field=description}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="2">{{tr}}CService.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>