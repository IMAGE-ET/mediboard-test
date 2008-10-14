{{assign var=line value=$_line_comment}}  

<tbody class="hoverable">
   <tr>
     <td>
       {{if $line->_can_vw_form_executant}}
         {{$line->_ref_executant->_view}}: 
       {{/if}}
       {{$line->commentaire}}
     </td>
     <td style="width: 0.1%;">
       <b>{{mb_label object=$line field="ald"}}</b>: {{if $line->ald}}Oui{{else}}Non{{/if}}
     </td>
     <td style="text-align: right;">
       <!-- Affichage de la signature du praticien -->
       {{if $line->_can_view_signature_praticien}}
         {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
       {{elseif !$line->_protocole}}
         {{$line->_ref_praticien->_view}}    
       {{/if}}
     </td>
  </tr>
</tbody>