{{include file=CMbObject_view.tpl}}

{{assign var=consultation value=$object}}

<table class="tbl">
  {{if $consultation->annule == 1}}
  <tr>
    <th class="category cancelled" colspan="4">
    {{tr}}CConsultation-annule{{/tr}}
    </th>
  </tr>
  {{/if}}
  <tr>
  
{{if $can->edit}}
  <tr>
    <td class="button">
      {{mb_include_script module="dPcabinet" script="consultation" ajax="true"}}

      <button type="button" class="edit" onclick="Consultation.edit('{{$consultation->_id}}')">
        {{tr}}Modify{{/tr}}
      </button>

      <button type="button" class="change" onclick="Consultation.plan('{{$consultation->_id}}')">
        {{tr}}Rendez-vous{{/tr}}
      </button>
    </td>
  </tr>
{{/if}}
</table>

{{mb_include module=dPcabinet template=inc_list_actes_ccam subject=$consultation vue=view}}
{{mb_include module=dPcabinet template=inc_list_actes_ngap subject=$consultation }}
 
{{assign var=examaudio value=$consultation->_ref_examaudio}}
{{if $examaudio && $examaudio->_id}}
<script type="text/javascript">
newExam = function(sAction, consultation_id) {
	if (sAction) {
		var url = new Url("dPcabinet", sAction);
		url.addParam("consultation_id", consultation_id);
		url.popup(900, 600, "Examen");  
	}
}
</script>
<a href="#{{$examaudio->_guid}}" onclick="newExam('exam_audio', '{{$consultation->_id}}')">
  <strong>Audiogramme</strong>
</a>
{{/if}}

