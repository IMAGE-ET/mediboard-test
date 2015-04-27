{{mb_default var=offline value=0}}

<table class="tbl">
  {{if $offline}}
    <thead>
      <tr>
        <th class="title" colspan="9">
          {{$sejour->_view}}
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
        </th>
      </tr>
    </thead>
  {{/if}}
  <tr>
    <th colspan="9" class="title">
      {{if !$readonly}}
        <div style="float: right">
          <input name="_show_obs_view" id="_show_obs_view" type="checkbox" {{if $_show_obs}}checked="checked"{{elseif $cible != ""}}disabled{{/if}}
            onclick="loadSuivi('{{$sejour->_id}}', '', '', this.checked ? 1 : 0, $('_show_trans_view').checked ? 1 : 0, !$('_show_const_view') ? null : $('_show_const_view').checked ? 1 : 0)"/>
          <label for="_show_obs_view" title="{{tr}}CObservationMedicale{{/tr}}">{{tr}}CObservationMedicale._show_obs{{/tr}}</label>
          
          <input name="_show_trans_view" id="_show_trans_view" type="checkbox" {{if $_show_trans}}checked="checked"{{/if}}
            onclick="loadSuivi('{{$sejour->_id}}', '', '', $('_show_obs_view').checked ? 1 : 0, this.checked ? 1 : 0, !$('_show_const_view') ? null : $('_show_const_view').checked ? 1 : 0)"/>
          <label for="_show_trans_view" title="{{tr}}CTransmissionMedicale{{/tr}}">{{tr}}CTransmissionMedicale._show_trans{{/tr}}</label>
          
          {{if "soins CConstantesMedicales constantes_show"|conf:"CGroups-$g"}}
            <input name="_show_const_view" id="_show_const_view" type="checkbox" {{if $_show_const}}checked="checked"{{elseif $cible != ""}}disabled{{/if}}
              onclick="loadSuivi('{{$sejour->_id}}', '', '', $('_show_obs_view').checked ? 1 : 0, $('_show_trans_view').checked ? 1 : 0, this.checked ? 1 : 0)"/>
            <label for="_show_const_view" title="{{tr}}CConstantesMedicales{{/tr}}">{{tr}}CConstantesMedicales._show_const{{/tr}}</label>
          {{/if}}
          
          <select style="width: 150px" name="selCible" onchange="loadSuivi('{{$sejour->_id}}','',this.value)" >
            <option value="">&mdash; Toutes les cibles</option>
            {{foreach from=$cibles item=cibles_by_state key=state}}
              {{if $cibles_by_state|@count}}
                <optgroup label="{{tr}}CTransmission.state.{{$state}}{{/tr}}"></optgroup>
                {{foreach from=$cibles_by_state item=_cible}}
                  <option {{if $_cible == $cible}} selected="selected" {{/if}} value="{{$_cible}}">{{$_cible|capitalize}}</option>
                {{/foreach}}
              {{/if}}
            {{/foreach}}
          </select>

          {{if @$users}}
          <select name="user_id" onchange="loadSuivi('{{$sejour->_id}}',this.value)">
            <option value="">&mdash; Tous les utilisateurs</option>
            {{foreach from=$users item=_user}}
              <option value="{{$_user->_id}}" {{if $user_id == $_user->_id}} selected="selected"{{/if}}>{{$_user->_view}}</option>
            {{/foreach}}
          </select>
          {{/if}}
        </div>
      {{/if}}
      <span style="float: left;"> Suivi de soins</span>
    </th>
  </tr>
  <tr>
    <th rowspan="2">{{tr}}Type{{/tr}}</th>
    <th rowspan="2">{{tr}}User{{/tr}} / {{tr}}Date{{/tr}}</th>
    <th rowspan="2">{{mb_title class=CTransmissionMedicale field=object_class}}</th>
    <th colspan="3" style="width: 50%">{{mb_title class=CTransmissionMedicale field=text}}</th>
    <th rowspan="2" class="narrow"></th>
  </tr>
  <tr>
    <th class="section" style="width: 17%">{{tr}}CTransmissionMedicale.type.data{{/tr}}</th>
    <th class="section" style="width: 17%">{{tr}}CTransmissionMedicale.type.action{{/tr}}</th>
    <th class="section" style="width: 17%">{{tr}}CTransmissionMedicale.type.result{{/tr}}</th>
  </tr>
  <tbody {{if !$readonly}} id="transmissions" {{/if}}>
  {{foreach from=$list_transmissions item=_suivi}}
  <tr class="{{if is_array($_suivi)}}
               print_transmission
             {{if $_suivi.0->degre == "high"}}
               transmission_haute
             {{/if}}
             {{if $_suivi.0->object_class}}
               {{$_suivi.0->_ref_object->_guid}}
             {{/if}}
           {{else}}
             {{$_suivi->_guid}}
             {{if $_suivi instanceof CTransmissionMedicale}}
               {{if $_suivi->degre == "high"}}
                 transmission_haute
               {{/if}}
             {{elseif $_suivi instanceof CConsultation && $_suivi->type == "entree"}}
               print_observation
               consultation_entree
             {{elseif $_suivi instanceof CObservationMedicale}}
               print_observation
               {{if $_suivi->degre == "info"}}
                 observation_info
               {{elseif $_suivi->degre == "high"}}
                 observation_urgente
               {{/if}}
             {{/if}}
           {{/if}}"
      {{if ($_suivi instanceof CPrescriptionLineElement || $_suivi instanceof CPrescriptionLineComment) && !$readonly}}
        onmouseover="highlightTransmissions('{{$_suivi->_guid}}');" onmouseout="removeHighlightTransmissions();"
      {{/if}}>
     {{mb_include module=hospi template=inc_line_suivi show_patient=false nodebug=true}}
    </tr>
  {{foreachelse}}
  </tbody>
    <tr>
      <td colspan="9" class="empty">{{tr}}CTransmissionMedicale.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>