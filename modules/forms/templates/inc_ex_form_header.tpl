{{mb_default var=parent_view value=null}}

{{if $ex_object->_ref_reference_object_2 && $ex_object->_ref_reference_object_2->_id}}
  <span style="color: #006600;"
    {{if !$readonly}}
      onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_reference_object_2->_guid}}');"
    {{/if}}>
    {{$ex_object->_ref_reference_object_2}}

    {{if $ex_object->_ref_reference_object_2 instanceof CPatient}}
      {{mb_include module=patients template=inc_vw_ipp ipp=$ex_object->_ref_reference_object_2->_IPP}}
    {{/if}}
  </span>

  {{* NDA, etc *}}
  {{if $ex_object->_ref_reference_object_2 instanceof CSejour}}
    {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$ex_object->_ref_reference_object_2}}
    {{$ex_object->_ref_reference_object_2->_ref_curr_affectation->_ref_lit}}
  {{/if}}
{{else}}
  {{if $ex_object->_rel_patient}}
    {{assign var=_patient value=$ex_object->_rel_patient}}
    <span style="color: #006600;"
      {{if !$readonly}}
        onmouseover="ObjectTooltip.createEx(this, '{{$_patient->_guid}}');"
      {{/if}}>
      {{$_patient}}
      {{mb_include module=patients template=inc_vw_ipp ipp=$_patient->_IPP}}
    </span>
  {{/if}}
{{/if}}

{{if $ex_object->_ref_reference_object_1 && $ex_object->_ref_reference_object_1->_id}}
  &ndash;
  <span
    {{if !$readonly}}
      onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_reference_object_1->_guid}}');"
    {{/if}}>
    {{if $ex_object->_ref_reference_object_1 instanceof CSejour}}
      {{$ex_object->_ref_reference_object_1->_ref_curr_affectation->_ref_lit}}
      {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$ex_object->_ref_reference_object_1}}
    {{else}}
      {{$ex_object->_ref_reference_object_1}}
    {{/if}}
  </span>
{{/if}}

&ndash;
<span style="color: #0000AA;"
  {{if !$readonly && $ex_object->_id}}
    onmouseover="ObjectTooltip.createEx(this, 'CExObject_{{$ex_object->_ex_class_id}}-{{$ex_object->_id}}', 'objectViewHistory')"
  {{/if}}>

  {{if !$readonly}}
    {{if $ex_object->_id}}
      <img src="images/icons/history.gif" width="16" height="16"/>
    {{else}}
      <img src="images/icons/new.png" width="16" height="16"/>
    {{/if}}
  {{/if}}

  {{$ex_object->_ref_last_log->_ref_user}}
</span>

{{if !$readonly}}
  <hr style="border-color: #333; margin: 4px 0;" />
{{else}}
  <br />
{{/if}}

{{$ex_object->_ref_ex_class->name}} -

<span
  {{if !$readonly}}
    onmouseover="ObjectTooltip.createEx(this, '{{$object->_guid}}')"
  {{/if}}>
  {{$object}}
</span>
{{if $object instanceof CSejour}}
  {{$object->_ref_curr_affectation->_ref_lit}}
  {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$object}}
{{/if}}

{{if $ex_object->additional_id}}
  <hr />
  <span style="color: #AA0000;"
    {{if !$readonly}}
      onmouseover="ObjectTooltip.createEx(this, '{{$ex_object->_ref_additional_object->_guid}}')"
    {{/if}}>
    {{$ex_object->_ref_additional_object}}
  </span>
{{/if}}

{{if $parent_view}}
  <span style="float: right; color: #666;">
    Formulaire parent: {{$parent_view|smarty:nodefaults}}
  </span>
{{/if}}