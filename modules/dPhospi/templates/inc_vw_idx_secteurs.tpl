<table class="tbl">
  <tr>
    <th colspan="4" class="title">
      <button type="button" onclick="Infrastructure.addeditSecteur('0')" class="button new compact" style="float:left;">
        {{tr}}CSecteur-title-create{{/tr}}
      </button>
      {{tr}}CSecteur.all{{/tr}}
    </th>
  </tr>
  <tr>
    <th></th>
    <th>{{mb_title class=CSecteur field=nom}}</th>
    <th>{{tr}}CService.all{{/tr}}</th>
    <th>{{mb_title class=CSecteur field=description}}</th>
  </tr>

  {{foreach from=$secteurs item=_secteur}}
  <tr>
    <td class="narrow">
      <button class="button edit notext compact" onclick="Infrastructure.addeditSecteur('{{$_secteur->_id}}')"></button>
    </td>
    <td class="text" style="width: 20%">{{mb_value object=$_secteur field=nom}}</td>
    <td class="narrow columns-2">
      {{foreach from=$_secteur->_ref_services item=_service}}
        <div class="compact">{{mb_value object=$_service field=nom}}</div>
        {{foreachelse}}
        {{tr}}CService.none{{/tr}}
      {{/foreach}}
    </td>
    <td class="text">{{mb_value object=$_secteur field=description}}</td>

  </tr>
  {{foreachelse}}
    <tr>
      <td class="empty" colspan="2">{{tr}}CSecteur.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

