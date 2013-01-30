{{if $facture->_id}}
  {{assign var=object value=$facture}}
{{/if}}

<fieldset>
  <legend>Règlements ({{tr}}{{$object->_class}}{{/tr}})</legend>
  {{if $object->du_patient}}
    {{mb_include  module=dPfacturation template=inc_vw_du_patient_reglements}}
  {{else}}
    {{mb_include  module=dPfacturation template=inc_vw_du_tiers_reglements}}
  {{/if}}
</fieldset>