{{mb_script module=cabinet script=edit_consultation ajax=true}}

{{mb_default var=patient_id value=""}}
{{mb_default var=sejour_id value=""}}
{{mb_default var=operation_id value=""}}

<button id="inc_vw_patient_button_consult_now" class="new" onclick="Consultation.openConsultImmediate('{{$patient_id}}', '{{$sejour_id}}', '{{$operation_id}}')">
  {{tr}}CConsultation-action-Immediate{{/tr}}
</button>