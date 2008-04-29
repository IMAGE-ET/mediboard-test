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

  {{foreach from=$prescription->_counts_by_chapitre key=chapitre item=count}}
  {{if $totals_by_chapitre.$chapitre}}
  <td style="text-align: center;">
    {{if $count}}{{$count}}{{else}}-{{/if}}
  </td>
  {{/if}}
  {{/foreach}}

</tr>