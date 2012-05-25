<table class="tbl">
  <tr>
    <th class="title" colspan="6">
      Liste des interventions
    </th>
  </tr>
  {{foreach from=$operations item=_operation}}
    <tr>
      <td>
        {{mb_include module=system template=inc_vw_mbobject object=$_operation->_ref_patient}}
      </td>
      <td>{{mb_value object=$_operation field=_datetime}}</td>
      <td>{{$_operation->duree_uscpo}} nuit(s)</td>
      <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_operation->_ref_chir}}</td>
      <td class="text">
        {{if $_operation->libelle}}
          {{$_operation->libelle}}
        {{else}}
          {{" ; "|implode:$_operation->_codes_ccam}}
        {{/if}}
      <td>
        <a class="button edit" href="?m=planningOp&tab=vw_edit_planning&operation_id={{$_operation->_id}}">Déplacer</a>
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td class="empty">
        {{tr}}COperation.none{{/tr}}
      </td>
    </tr>
  {{/foreach}}
</table>