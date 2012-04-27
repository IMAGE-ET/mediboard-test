{{assign var=patient value=$_operation->_ref_sejour->_ref_patient}}
{{assign var=chir    value=$_operation->_ref_chir}}

<tbody class="hoverable">
  <tr>
    <td rowspan="2" style="vertical-align: middle;">
      {{if $_operation->rank}}
        <div class="rank">{{$_operation->rank}}</div>
      {{elseif $_operation->rank_voulu}}
        <div class="rank desired" title="Pas encore validé par le bloc">{{$_operation->rank_voulu}}</div>
      {{/if}}
    </td>
    <td rowspan="2">
      <strong>
        {{if $_operation->rank}}
          {{mb_value object=$_operation field=time_operation}}
          <img src="images/icons/tick.png" title="Validé" />
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
    <td colspan="3">{{mb_include template=inc_vw_operation}}</td>
  </tr>
</tbody>
