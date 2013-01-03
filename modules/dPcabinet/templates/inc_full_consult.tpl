{{if "dPmedicament"|module_active}}
  {{mb_script module="dPmedicament" script="medicament_selector"}}
  {{mb_script module="dPmedicament" script="equivalent_selector"}}
{{/if}}

{{if "dPprescription"|module_active}}
  {{mb_script module="dPprescription" script="prescription"}}
  {{mb_script module="dPprescription" script="prescription_editor"}}
  {{mb_script module="dPprescription" script="element_selector"}}
{{/if}}

{{mb_script module="dPcompteRendu" script="document"}}
{{mb_script module="dPcompteRendu" script="modele_selector"}}

{{if $consult->_id}}
{{assign var="patient" value=$consult->_ref_patient}}
<div id="finishBanner">
{{include file="../../dPcabinet/templates/inc_finish_banner.tpl"}}
</div>
{{include file="../../dPcabinet/templates/inc_patient_infos_accord_consult.tpl"}}
{{include file="../../dPcabinet/templates/acc_consultation.tpl"}}
{{/if}}
    