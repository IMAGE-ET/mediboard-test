{{mb_script module="dPplanningOp" script="operation" ajax=1}}
<table class="tbl">
  <tr>
    <th colspan="2" class="category">Libell�s</th>
  </tr>
  {{foreach from=$liaisons item=liaison key=key}}
    <tr>
      <th class="narrow">
        Libell� {{$liaison->numero}}
      </th>
      <td>
        {{if $liaison->_id}} {{$liaison->_ref_libelle->nom}}{{/if}}
      </td>
    </tr>
  {{/foreach}}
  <tr>
    <td colspan="2" class="button">
      <button class="edit" type="button" onclick="LiaisonOp.edit('{{$operation_id}}');">Modifier les libell�s</button>
    </td>
  </tr>
</table>
