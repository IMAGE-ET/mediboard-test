{{assign var=operation value=$object}}
{{assign var=sejour    value=$object->_ref_sejour}}
{{assign var=patient   value=$sejour->_ref_patient}}

<table class="tbl tooltip">
  <tr>
    <th colspan="3">
      {{mb_include module=system template=inc_object_notes     }}
      {{mb_include module=system template=inc_object_idsante400}}
      {{mb_include module=system template=inc_object_history   }}
      {{$object}}
    </th>
  </tr>
  <tr>
    <td rowspan="3">
      {{mb_include module=patients template=inc_vw_photo_identite mode=read patient=$patient size=50}}
    </td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$sejour->_guid}}')">
        {{$sejour}}
      </span>
    </td>
  </tr>
  <tr>
    <td>
      {{mb_label object=$object field=libelle}} : {{mb_value object=$object field=libelle}}
    </td>
  </tr>
  <tr>
    <td>
      {{mb_label object=$object field=cote}} {{mb_value object=$object field=cote}}
    </td>
  </tr>
  <tr>
    <td colspan="2">
      {{mb_value object=$object field=_datetime}} &mdash; {{$object->_ref_salle}}
    </td>
  </tr>
  <tr>
    <td colspan="2" class="text">
      Remarques : {{mb_value object=$object field=rques}}
    </td>
  </tr>
  
  {{if $operation->debut_op}}
    <tr colspan="4">
      <td>
        <strong>{{tr}}COperation-debut_op{{/tr}} :</strong>
        {{$operation->debut_op|date_format:$conf.time}}
      </td>
      <td>
      </td>
    </tr>
  {{/if}}
  {{if $operation->annulee == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}COperation-annulee{{/tr}}
    </th>
  </tr>
  {{/if}}
  
  <tr>
    <td class="button" colspan="4">
      {{mb_script module="dPplanningOp" script="operation" ajax="true"}}
      
      {{if $can->edit}}
        <button type="button" class="edit" onclick="Operation.edit('{{$operation->_id}}', '{{$operation->plageop_id}}');">
          {{tr}}Modify{{/tr}}
        </button>
      {{/if}}

      <button type="button" class="print" onclick="Operation.print('{{$operation->_id}}');">
        {{tr}}Print{{/tr}}
      </button>
    </td>
  </tr>
</table>

<table class="tbl tooltip">
  {{mb_include module=cabinet template=inc_list_actes_ccam subject=$operation vue=view}}
</table>
