{{* $Id: $ *}}

{{if $prescriptionItem->_id}}
{{assign var="examen" value=$prescriptionItem->_ref_examen_labo}}
{{assign var="patient" value=$prescriptionItem->_ref_prescription_labo->_ref_patient}}
<div id="resultGraph" style="text-align: center;">
  <img alt="Graph des résultats" src='?m=dPlabo&amp;a=graph_resultats&amp;suppressHeaders=1&amp;patient_id={{$patient->_id}}&amp;examen_id={{$examen->_id}}&amp;time={{$time}}' />
</div>
{{/if}}