{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=dosql value="do_prescription_line_comment_aed"}}
			
{{if $line->category_prescription_id}}
	<!-- Commentaire d'elements -->
	{{assign var=category value=$line->_ref_category_prescription}}
	{{assign var=div_refresh value=$line->_chapitre}}
{{else}}
  <!-- Commentaires de medicaments -->
  {{assign var=div_refresh value="medicament"}}  
{{/if}}	        

<table>
<tbody class="hoverable">
   <tr>
     <td style="width: 25px">
       {{if $line->_can_delete_line}}
	       <form name="delLineComment-{{$line->_id}}" action="" method="post">
	         <input type="hidden" name="m" value="dPprescription" />
	         <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	         <input type="hidden" name="del" value="1" />
	         <input type="hidden" name="prescription_line_comment_id" value="{{$line->_id}}" />
	         <button type="button" class="trash notext" onclick="modalPrescription.close(); return onSubmitFormAjax(this.form, { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'{{$div_refresh}}','{{$mode_protocole}}','{{$mode_pharma}}') } } );">
	           {{tr}}Delete{{/tr}}
	         </button>
	       </form>
       {{/if}}
     </td>
     <td style="width: 80%;" class="text">
       {{if $line->_perm_edit}}
       <form name="editCommentaire-{{$line->_guid}}">
         <input type="hidden" name="m" value="dPprescription" />
	       <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	       <input type="hidden" name="del" value="0" />
	       <input type="hidden" name="prescription_line_comment_id" value="{{$line->_id}}" />
         {{mb_field object=$line field=commentaire onchange="submitFormAjax(this.form, 'systemMsg');"}}
       </form>
       {{else}}
         {{mb_value object=$line field=commentaire}}
       {{/if}}
     </td>
     <td>
       {{if $line->category_prescription_id}}
	       {{if $line->_can_vw_form_executant}}
	         <div style="float: right">
	           {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
	         </div>
	       {{/if}}
       {{/if}}
     </td>
     <td>
        {{if $prescription->type != "sejour"}}
         {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
       {{/if}}
     </td>  
     <td style="text-align: right">
       <div class="mediuser" {{if !$line->_protocole}}style="border-color: #{{$line->_ref_praticien->_ref_function->color}};"{{/if}}>
	       <!-- Affichage de la signature du praticien -->
	       {{if $line->_can_view_signature_praticien}}
	         {{include file="../../dPprescription/templates/line/inc_vw_signature_praticien.tpl"}}
	       {{elseif !$line->_protocole}}
	         {{$line->_ref_praticien->_view}}    
	       {{/if}}
	       <!-- Affichage du formulaire de signature du praticien --> 
	       {{if $line->_can_view_form_signature_praticien}} 
		       {{include file="../../dPprescription/templates/line/inc_vw_form_signature_praticien.tpl"}}
		     {{/if}}
	       <button class="lock notext" onclick="modalPrescription.close(); Prescription.reload('{{$prescription->_id}}', '', '{{$div_refresh}}', '', '{{$mode_pharma}}', null, '');"></button>
       </div>
	   </td>
  </tr>
</tbody>
</table>