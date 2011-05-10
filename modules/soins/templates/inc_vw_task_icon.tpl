<a href="#1" style="float: right">
{{if $line->_ref_task->_id}}
  {{if $line->_ref_task->realise}}
    <img src="images/icons/phone_green.png" title="RDV réalisé" onclick="PlanSoins.editTask('{{$prescription->object_id}}', '{{$line->_id}}');" />
  {{else}}
    <img src="images/icons/phone_orange.png" title="RDV pris" onclick="PlanSoins.editTask('{{$prescription->object_id}}', '{{$line->_id}}');" />
  {{/if}}
{{else}}
  <img src="images/icons/phone_red.png" title="RDV à prendre" onclick="PlanSoins.editTask('{{$prescription->object_id}}', '{{$line->_id}}');" />
{{/if}}
</a>