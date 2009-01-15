{{assign var=line value=$_line_comment}}  

{{if $line->category_prescription_id}}
	<!-- Commentaire d'elements -->
	{{assign var=category value=$line->_ref_category_prescription}}
	{{assign var=div_refresh value=$element}}
{{else}}
  <!-- Commentaires de medicaments -->
  {{assign var=div_refresh value="medicament"}}  
{{/if}}	  

<tbody class="hoverable">
   <tr>
     <td colspan="4">
       {{$line->commentaire}}
     </td>
     <td style="text-align: right;" colspan="2">
     {{if $line->category_prescription_id}}
       <b>Exécutant</b>: {{if $line->executant_prescription_line_id || $line->user_executant_id}}{{$line->_ref_executant->_view}}{{else}}aucun{{/if}}
     {{/if}}
       <b>{{mb_label object=$line field="ald"}}</b>: {{if $line->ald}}Oui{{else}}Non{{/if}}
       
       <!-- Affichage de la signature du praticien -->
       {{if $line->_can_view_signature_praticien}}
         {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
       {{elseif !$line->_protocole}}
         {{$line->_ref_praticien->_view}}    
       {{/if}}
       <button class="edit notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', '{{$div_refresh}}', '', '{{$mode_pharma}}', null, true, {{$lite}},'{{$line->_guid}}');"></button>
     </td>
  </tr>
</tbody>