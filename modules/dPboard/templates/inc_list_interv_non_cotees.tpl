{{if $board}}
<script>
  updateNbActes({{$interventions|@count}});
</script>
{{/if}}

<table class="tbl">
  <tr>
    <th>Patient</th>
    <th>Intervention</th>
    <th class="narrow">Actes <br /> Non cotés</th>
    <th class="narrow">Codes <br /> prévus   </th>
    <th>Actes cotés</th>
  </tr>

  <tr>
    <th class="section" colspan="5">Interventions</th>
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
      <td class="text">
        {{assign var=patient value=$_interv->_ref_patient}}
        {{assign var=sejour  value=$_interv->_ref_sejour}}
        <a href="{{$patient->_dossier_cabinet_url}}">
          <strong class="{{if !$sejour->entree_reelle}}patient-not-arrived{{/if}}"
            onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
            {{$patient}}
          </strong>
        </a>
      </td>
      <td class="text">
        <a href="#1" onclick="Operation.dossierBloc('{{$_interv->_id}}', updateActes); return false;">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_interv->_guid}}')">
            {{$_interv}}
          </span>
        </a>
      </td>
      <td>
        {{if !$_interv->_count_actes && !$_interv->_ext_codes_ccam}}
          (Aucun prévu)
        {{else}}
          {{$_interv->_actes_non_cotes}} acte(s)
        {{/if}}
      </td>
      <td class="text">
        {{mb_include module=planningOp template=inc_vw_operation _operation=$_interv}}
      </td>
      
      <td>
        {{foreach from=$_interv->_ref_actes_ccam item=_acte}}
          <div class="">
            {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_acte->_ref_executant initials=border}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_acte->_guid}}')">
              {{$_acte->code_acte}}-{{$_acte->code_activite}}-{{$_acte->code_phase}}
              {{if $_acte->modificateurs}}
                MD:{{$_acte->modificateurs}}
              {{/if}}
              {{if $_acte->montant_depassement}}
                DH:{{$_acte->montant_depassement|currency}}
              {{/if}}
            </span>
          </div>
        {{/foreach}}
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}COperation.none_non_cotee{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>