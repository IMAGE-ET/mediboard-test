<table class="tbl">
  <tr>
    <th class="title" colspan="3">
      {{tr}}CRessourceSoin-list{{/tr}}
    </th>
  </tr>
  <tr>
    <th class="category narrow">{{mb_label class=CRessourceSoin field=code}}</th>
    <th class="category">{{mb_label class=CRessourceSoin field=libelle}}</th>
    <th class="category narrow">{{mb_label class=CRessourceSoin field=cout}}</th>
  </tr>
  {{foreach from=$ressources_soins item=_ressource_soin}}
    <tr {{if $_ressource_soin->_id == $ressource_soin_id}}class="selected"{{/if}}>
      <td>
        <strong>{{mb_value object=$_ressource_soin field=code}}</strong>
      </td>
      <td>
        <a href="#{{$_ressource_soin->_id}}" onclick="Ressource.edit('{{$_ressource_soin->_id}}', null, this.up('tr'))">
          {{mb_value object=$_ressource_soin field=libelle}}
        </a>
      </td>
      <td style="text-align: right;">
        {{mb_value object=$_ressource_soin field=cout}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="2" class="empty">
        {{tr}}CRessourceSoin.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>