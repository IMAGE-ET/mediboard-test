{{if $board}}
<script>
  updateNbActes({{$interventions|@count}});
</script>
{{/if}}

<table class="tbl">
  <tr>
    <th colspan="4">Interventions</th>
  </tr>
  {{foreach from=$interventions item=_interv}}
    {{assign var=codes_ccam value=$_interv->codes_ccam}}
    <tr>
      {{if $all_prats}}
        <td class="text">
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_interv->_ref_chir}}
          {{if $_interv->_ref_anesth}}
          <br />
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_interv->_ref_anesth}}
          {{/if}}
        </td>
      {{/if}}
      <td class="narrow">
        {{assign var=patient value=$_interv->_ref_patient}}
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
          {{$patient}}
        </span>
      </td>
      <td class="text">
        <a href="#1" onclick="Operation.dossierBloc('{{$_interv->_id}}', updateActes); return false;">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_interv->_guid}}')">
            {{$_interv}}
            {{if !$_interv->_count_actes && !$_interv->_ext_codes_ccam}}
              (Aucun acte prévu ou coté)
            {{else}}
              ({{$_interv->_actes_non_cotes}} acte(s) non coté(s))
            {{/if}}
          </span>
        </a>
      </td>
      <td class="text">{{mb_include module=planningOp template=inc_vw_operation _operation=$_interv}}</td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}COperation.none_non_cotee{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>