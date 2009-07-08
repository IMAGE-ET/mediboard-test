<table class="tbl">
  <tr>
    <th>Patient</th>
    <th>Praticien</th>
    <th>Durée</th>
  </tr>
  {{foreach from=$listSalles item=_salle}}
  <tr>
    <th colspan="3">{{$_salle->_view}}</th>
  </tr>
  {{foreach from=$_salle->_list_patients_reveil item=_interv}}
  <tr {{if $op_reveil_id == $_interv->_id}}class="selected"{{/if}}>
    <td class="text">
      <a href="?m=dPsalleOp&tab=vw_soins_reveil&op_reveil_id={{$_interv->_id}}" />
        {{$_interv->_ref_sejour->_ref_patient->_view}}
      </a>
    </td>
    <td class="text">
      <a href="?m=dPsalleOp&tab=vw_soins_reveil&op_reveil_id={{$_interv->_id}}" />
        Dr {{$_interv->_ref_chir->_view}}
      </a>
    </td>
    <td class="text">
      {{$_interv->_presence_reveil|date_format:$dPconfig.time}}
    </td>
  </tr>
  {{/foreach}}
  {{/foreach}}
</table>