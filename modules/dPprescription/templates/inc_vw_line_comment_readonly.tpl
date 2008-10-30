{{assign var=line value=$_line_comment}}  

<tbody class="hoverable">
   <tr>
     <td colspan="2">
       {{$line->commentaire}}
     </td>
     <td style="text-align: right;">
     <b>Exécutant</b>: {{if $line->executant_prescription_line_id || $line->user_executant_id}}{{$line->_ref_executant->_view}}{{else}}aucun{{/if}}
       <b>{{mb_label object=$line field="ald"}}</b>: {{if $line->ald}}Oui{{else}}Non{{/if}}
       
       <!-- Affichage de la signature du praticien -->
       {{if $line->_can_view_signature_praticien}}
         {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
       {{elseif !$line->_protocole}}
         {{$line->_ref_praticien->_view}}    
       {{/if}}
     </td>
  </tr>
</tbody>


