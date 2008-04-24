{{*mb_ternary var=perm_edit test=$_line_comment->signee value="0" other="1"*}}

{{if ($_line_comment->praticien_id == $app->user_id) && !$_line_comment->signee}}
  {{assign var=perm_edit value="1"}}
{{else}}
  {{assign var=perm_edit value="0"}}
{{/if}}


{{assign var=line value=$_line_comment}}
{{assign var=dosql value="do_prescription_line_comment_aed"}}
			
{{if $_line_comment->category_prescription_id}}
	<!-- Commentaire d'elements -->
	{{assign var=category value=$_line_comment->_ref_category_prescription}}
	{{assign var=div_refresh value=$element}}
{{else}}
  <!-- Commentaires de medicaments -->
  {{assign var=div_refresh value="medicament"}}  
{{/if}}	        

<tbody class="hoverable">
   <tr>
     <td style="width: 25px">
       {{if $perm_edit}}
	       <form name="delLineComment-{{$_line_comment->_id}}" action="" method="post">
	         <input type="hidden" name="m" value="dPprescription" />
	         <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	         <input type="hidden" name="del" value="1" />
	         <input type="hidden" name="prescription_line_comment_id" value="{{$_line_comment->_id}}" />
	         <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'{{$div_refresh}}') } } );">
	           {{tr}}Delete{{/tr}}
	         </button>
	       </form>
       {{/if}}
     </td>
     <td>
       {{if $_line_comment->_class_name == "CPrescriptionLineElement"}}
         <div style="float: right">
           {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
         </div>
       {{/if}}
       {{$_line_comment->commentaire}}
     </td>
     
     <td style="width: 25px">
       {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
     </td>
     <td style="text-align: right">
       <!-- Affichage de la signature du praticien -->
       {{if ($_line_comment->praticien_id != $app->user_id) && !$_line_comment->_protocole}}
         {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
       {{else}}
         {{$_line_comment->_ref_praticien->_view}}    
       {{/if}}
       
       <!-- Affichage du formulaire de signature du praticien --> 
       {{if !$_line_comment->_protocole}}  
	       {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
	     {{/if}}
     </td>
  </tr>
</tbody>