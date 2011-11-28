<table class="tbl">
  <tr>
    <th class="category" colspan="3">
      Programme du {{mb_value object=$plageop field=date}}
      <br />
      {{$plageop->debut|date_format:$conf.time}} -
      {{$plageop->fin|date_format:$conf.time}}
      &mdash; {{$plageop->_ref_salle->_view}}
    </th>
  </tr>
  
  {{foreach from=$plageop->_ref_operations item=_operation}}
  {{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
  {{assign var=chir    value=$_operation->_ref_chir}}
  <tbody class="hoverable">
  <tr>
    <td rowspan="2">
      <strong>
      {{if $_operation->rank}}
        {{mb_value object=$_operation field=time_operation}}
        <img src="images/icons/tick.png" title="validé" />
      {{elseif $_operation->horaire_voulu}}
        {{mb_value object=$_operation field=horaire_voulu}}
      {{else}}
        NP
      {{/if}}
      </strong>
      <br />
      <em>({{mb_value object=$_operation field=temp_operation}})</em>
    </td>
    <td>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$chir}}</td>
    <td>
      <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">{{$patient}}</span>
    </td>
  </tr>
  <tr>
    <td colspan="2">{{mb_include template=inc_vw_operation}}</td>
  </tr>
  </tbody>
  {{foreachelse}}
  <tr>
    <td colspan="3">{{tr}}COperation.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>
  