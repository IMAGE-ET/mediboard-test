{{if $board}}
<script>
  Control.Tabs.setTabCount('actes_non_cotes', {{$interventions|@count}} + {{$consultations|@count}});
</script>
{{/if}}

{{mb_script module=cabinet script=edit_consultation ajax=true}}

<table class="tbl">
  <tr>
    <th>Patient</th>
    <th>Evènement</th>
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
        {{if $sejour->libelle}}
        <div class="compact">
            {{$sejour->libelle}}
        </div>
        {{/if}}
        {{if $_interv->libelle}}
        <div class="compact">
            {{$_interv->libelle}}
        </div>
        {{/if}}
      </td>
      <td>
        {{if !$_interv->_count_actes && !$_interv->_ext_codes_ccam}}
          <div class="empty">Aucun prévu</div>
        {{else}}
          {{$_interv->_actes_non_cotes}} acte(s)
        {{/if}}
      </td>
      <td class="text">
        {{foreach from=$_interv->_ext_codes_ccam item=code}}
          <div>
            {{$code->code}}
          </div>
        {{/foreach}}
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
      <td colspan="5" class="empty">{{tr}}COperation.none_non_cotee{{/tr}}</td>
    </tr>
  {{/foreach}}

  <tr>
    <th class="section" colspan="5">Consultations</th>
  </tr>
  {{foreach from=$consultations item=consult}}
    {{assign var=patient value=$consult->_ref_patient}}
    {{assign var=sejour value=$consult->_ref_sejour}}
    <tr>
      <td class="text">
        <a href="{{$patient->_dossier_cabinet_url}}">
          <strong class="{{if !$consult->_ref_sejour->entree_reelle}}patient-not-arrived{{/if}}"
                  onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}');">
            {{$patient}}
          </strong>
        </a>
      </td>
      <td>
        {{if $modules.dPcabinet->_can->read && !@$offline}}
          <a href="#1" onclick="Consultation.editModal('{{$consult->_id}}');return false;">
        {{else}}
          <a href="#1" title="Impossible d'accéder à la consultation">
        {{/if}}
          <span onmouseover="ObjectTooltip.createEx(this, '{{$consult->_guid}}')">
            Consultation le {{$consult->_datetime|date_format:$conf.date}}
          </span>
        </a>
        {{if $sejour->libelle}}
          <div class="compact">{{$sejour->libelle}}</div>
        {{/if}}
      </td>

      <td>
        {{if !$consult->_count_actes && !$consult->_ext_codes_ccam}}
          (Aucun prévu)
        {{else}}
          {{$consult->_actes_non_cotes}} acte(s)
        {{/if}}
      </td>

      <td class="text">
        {{foreach from=$consult->_ext_codes_ccam item=code}}
          <div>{{$code->code}}</div>
        {{/foreach}}
      </td>

      <td>
        {{foreach from=$consult->_ref_actes_ccam item=_acte}}
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
      <td colspan="5" class="empty">{{tr}}CConsultation.none_non_cotee{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>