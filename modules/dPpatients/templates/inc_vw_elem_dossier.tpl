<!-- $Id: $ -->

{{if $object->_class_name == "CSejour"}}

{{if $object->group_id == $g || $dPconfig.dPpatients.CPatient.multi_group == "full"}}
<tr>
  <td class="text">
    {{if $object->_canEdit}}
    <a class="actionPat" title="Modifier le séjour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$object->_id}}">
      <img src="images/icons/planning.png" alt="Planifier"/>
    </a>
    <a
       {{if $canAdmissions->view}}
       href="?m=dPadmissions&amp;tab=vw_idx_admission&amp;date={{$object->_date_entree_prevue}}#adm{{$object->_id}}"
       {{else}}
       href="#nothing"
       {{/if}}
    >
    {{/if}}
    {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$object->_num_dossier}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
    {{$object->_shortview}}
    {{if $object->_nb_files_docs}}
      - ({{$object->_nb_files_docs}} Doc.)
    {{/if}}
    </span>
    {{if $object->_canEdit}}
    </a>
    {{/if}}
  </td>
  <td {{if $object->annule}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_praticien}}
  </td>
</tr>

{{foreach from=$object->_ref_consultations item=_consult}}
<tr>
  <td class="text" style="text-indent: 1em;">
    <a href="#" class="iconed-text {{$_consult->_type}}">
      <span onmouseover="ObjectTooltip.createEx(this, '{{$_consult->_guid}}')">
      Consultation le {{$_consult->_datetime|date_format:$dPconfig.date}}
      {{if $_consult->_nb_files_docs}}
        - ({{$_consult->_nb_files_docs}} Doc.)
      {{/if}}
      </span>
    </a>
  </td>
  <td {{if $_consult->annule}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_chir}}
  </td>
</tr>
{{/foreach}}

{{foreach from=$object->_ref_operations item=curr_op}}
<tr>
  <td class="text" style="text-indent: 1em;">
    {{if $curr_op->_canEdit}}
    <a class="actionPat" title="Modifier l'intervention" href="{{$curr_op->_link_editor}}">
      <img src="images/icons/planning.png" alt="modifier"/>
    </a>
    <a href="{{$curr_op->_link_editor}}" class="iconed-text interv">
    {{else}}
    <a href="#nothing" class="iconed-text interv">
    {{/if}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_op->_guid}}')">
      Intervention le {{$curr_op->_datetime|date_format:$dPconfig.date}}
      {{if $curr_op->_nb_files_docs}}
        - ({{$curr_op->_nb_files_docs}} Doc.)
      {{/if}}
      </span>
    </a>
  </td>
  <td {{if $curr_op->annulee}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$curr_op->_ref_chir}}
  </td>
</tr>
{{/foreach}}
{{elseif $dPconfig.dPpatients.CPatient.multi_group == "limited" && !$object->annule}}
<tr>
  <td>
    {{$object->_shortview}}
  </td>
  <td style="background-color:#afa">
    {{$object->_ref_group->text|upper}}
  </td>
</tr>
{{/if}}

{{elseif $object->_class_name == "CConsultation"}}

{{if $object->_ref_chir->_ref_function->group_id == $g || $dPconfig.dPpatients.CPatient.multi_group == "full"}}
<tr>
  <td class="text">
    {{if $object->_canEdit}}
    <a class="actionPat" title="Modifier la consultation" href="?m=dPcabinet&amp;tab=edit_planning&amp;consultation_id={{$object->_id}}">
      <img src="images/icons/planning.png" alt="modifier" />
    </a>
    <a class="iconed-text {{$object->_type}}" 
      href="?m=dPcabinet&amp;tab=edit_consultation&amp;selConsult={{$object->_id}}&amp;chirSel={{$object->_ref_plageconsult->chir_id}}">
    {{else}}
    <a href="#nothing" class="iconed-text {{$object->_type}}">
    {{/if}}
    <span onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')">
      Le {{$object->_datetime|date_format:$dPconfig.datetime}} - {{$object->_etat}}
    </span>
    </a>
  </td>
  <td {{if $object->annule}}class="cancelled"{{/if}}>
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$object->_ref_chir}}
  </td>
</tr>
{{elseif $dPconfig.dPpatients.CPatient.multi_group == "limited" && !$object->annule}}
<tr>
  <td>
    Le {{$object->_datetime|date_format:$dPconfig.datetime}}
  </td>
  <td style="background-color:#afa">
    {{$object->_ref_chir->_ref_function->_ref_group->text|upper}}
  </td>
</tr>
{{/if}}

{{/if}}