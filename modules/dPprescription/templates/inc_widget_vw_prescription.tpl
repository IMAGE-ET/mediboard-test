<tr>
  <td>
   {{if $prescription->type != "traitement"}}
   <a href="#{{$prescription->_id}}" onclick="PrescriptionEditor.popup('{{$prescription->_id}}','{{$object_id}}','{{$object_class}}','{{$praticien_id}}');">
   {{tr}}CPrescription.type.{{$prescription->type}}{{/tr}}
   </a>
   {{else}}
   {{tr}}CPrescription.type.{{$prescription->type}}{{/tr}}
   {{/if}}
  </td>
  <td>
    {{$prescription->_ref_praticien->_view}}
  </td>
  {{foreach from=$prescription->_count item=nb}}
  <td>
    {{$nb}}
  </td>
  {{/foreach}}
</tr>