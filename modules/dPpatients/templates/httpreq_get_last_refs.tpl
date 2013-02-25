<table width="100%">
  <tr>
    <td style="font-weight: bold;">
      <a href="?m=dPpatients&amp;tab=vw_full_patients&amp;patient_id={{$patient->_id}}">
        <span onmouseover="ObjectTooltip.createEx(this, '{{$patient->_guid}}')">
           {{$patient->_view}} &mdash; {{$patient->_age}}
        </span>
      </a>
    </td>
  </tr>
  <tr>
    <td class="text">
      <strong>Séjours: </strong>
      <ul>
      {{foreach from=$patient->_ref_sejours item=_sejour}}
        <li>
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_sejour->_guid}}')">
            {{$_sejour}} 
          </span>
          <ul>
          {{foreach from=$_sejour->_ref_operations item=_op}}
            <li style="list-style-type: none;" class="iconed-text interv">
              {{if $is_anesth}}
                {{assign var=operations_ids value="CMbArray::pluck"|static_call:$consultation->_refs_dossiers_anesth:"operation_id"}}
                <input type="radio" name="_operation_id" value="{{$_op->operation_id}}" {{if in_array($_op->operation_id, $operations_ids)}}checked="checked"{{/if}}/>
              {{/if}}
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_op->_guid}}')">
                Intervention le {{mb_value object=$_op field=_datetime}}
              </span>
              avec le Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_chir}} 
              {{if $_op->annulee}}<span style="color: red;">[ANNULE]</span>{{/if}}
            </li>
          {{foreachelse}}
            <li class="empty">{{tr}}COperation.none{{/tr}}</li>
          {{/foreach}}
          
          {{foreach from=$_sejour->_ref_consultations item=_consult}}
            <li style="list-style-type: none;" class="iconed-text {{$_consult->_type}}">
              <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
              Consultation le  {{mb_value object=$_consult field=_datetime}}
              </span>
              avec le Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}} 
              {{if $_consult->annule}}<span style="color: red;">[ANNULE]</span>{{/if}}
            </li>
          {{/foreach}}
          </ul>
        </li>
      {{foreachelse}}
        <li class="empty">{{tr}}CSejour.none{{/tr}}</li>
      {{/foreach}}
      </ul>
    </td>
  </tr>
  
  <tr>
    <td class="text">
      <strong>Consultations:</strong>
      <ul>
        {{foreach from=$patient->_ref_consultations item=_consult}}
          <li class="iconed-text {{$_consult->_type}}" >
            {{assign var=facture value=$_consult->_ref_facture}}
            <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
              Consultation le  {{mb_value object=$_consult field=_datetime}}
            </span>
            {{if count($facture->_ref_notes)}}
              {{mb_include module=system template=inc_object_notes object=$facture float=left}}
            {{/if}}
              avec le Dr {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}} 
             {{if $_consult->annule}}<span style="color: red;">[ANNULE]</span>{{/if}}
          </li>
        {{foreachelse}}
          <li class="empty">{{tr}}CConsultation.none{{/tr}}</li>
        {{/foreach}}
      </ul>
    </td>
  </tr>
</table>