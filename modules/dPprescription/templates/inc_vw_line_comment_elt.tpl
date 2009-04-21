{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=line value=$_line_comment}}
{{assign var=dosql value="do_prescription_line_comment_aed"}}
			
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
     <td style="width: 25px">
       {{if $line->_can_delete_line}}
	       <form name="delLineComment-{{$line->_id}}" action="" method="post">
	         <input type="hidden" name="m" value="dPprescription" />
	         <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	         <input type="hidden" name="del" value="1" />
	         <input type="hidden" name="prescription_line_comment_id" value="{{$line->_id}}" />
	         <button type="button" class="trash notext" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'{{$div_refresh}}') } } );">
	           {{tr}}Delete{{/tr}}
	         </button>
	       </form>
       {{/if}}
     </td>
     <td style="width: 80%;" class="text">
       {{$line->commentaire}}
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
        {{if $prescription_reelle->type != "sejour"}}
         {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
       {{/if}}
     </td>  
     <td style="text-align: right">
       <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
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
		     {{if ($line->_guid == $full_line_guid) && $readonly}} 
	         <button class="lock notext" onclick="Prescription.reload('{{$prescription_reelle->_id}}', '', '{{$div_refresh}}', '', '{{$mode_pharma}}', null, '{{$readonly}}', '{{$lite}}','');"></button>
	       {{/if}}
       </div>
	   </td>
  </tr>
</tbody>