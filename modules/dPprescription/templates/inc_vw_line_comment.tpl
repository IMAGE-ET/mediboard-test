{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=dosql value="do_prescription_line_comment_aed"}}
{{mb_default var=advanced_prot value=0}}

{{if $line->category_prescription_id}}
	<!-- Commentaire d'elements -->
	{{assign var=category value=$line->_ref_category_prescription}}
	{{assign var=div_refresh value=$line->_chapitre}}
{{else}}
  <!-- Commentaires de medicaments -->
  {{assign var=div_refresh value="medicament"}}  
{{/if}}	        

{{mb_include module="dPprescription" template="inc_header_line"}}

<table class="tbl">
<tbody class="hoverable">
	 <tr>
	   <th class="category">
	   	  <div style="float: left">
				 {{if $line->_can_delete_line && !$advanced_prot}}
	         <form name="delLineComment-{{$line->_id}}" action="" method="post">
	           <input type="hidden" name="m" value="dPprescription" />
	           <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
	           <input type="hidden" name="del" value="1" />
	           <input type="hidden" name="prescription_line_comment_id" value="{{$line->_id}}" />
	           <button type="button" class="trash notext" onclick="
	             if (Prescription.confirmDelLine('{{$line->_view|smarty:nodefaults|JSAttribute}}')) {
                 modalPrescription.close();
                 return onSubmitFormAjax(this.form, { onComplete: function() {
                   Prescription.reload('{{$prescription->_id}}',null,'{{$div_refresh}}','{{$mode_protocole}}','{{$mode_pharma}}') } } );
	             }">
	             {{tr}}Delete{{/tr}}
	           </button>
	         </form>
          {{/if}}
			 
			    {{if $prescription->type != "sejour"}}
            {{include file="../../dPprescription/templates/line/inc_vw_form_ald.tpl"}}
          {{/if}}
				</div>
				 
			  <div class="mediuser" style="{{if !$line->_protocole}}border-color: #{{$line->_ref_praticien->_ref_function->color}};{{/if}} float: right;">
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
         <button class="lock notext" onclick="modalPrescription.close(); Prescription.reload.defer('{{$prescription->_id}}', '', '{{$div_refresh}}', '', '{{$mode_pharma}}', null, '', null, {{$advanced_prot}});"></button>
       </div>
			 <strong style="font-size: 1.5em;">
			   Ligne de commentaire
			 </strong>
		 </th>
	 </tr>	
   <tr>
     <td class="text">
       {{if $line->_perm_edit}}
			 
			   {{if $line->_protocole}}
		       {{assign var=_line_praticien_id value=$app->user_id}}
		     {{else}}
		       {{assign var=_line_praticien_id value=$line->praticien_id}}
		     {{/if}}
         
	       <form name="editCommentaire-{{$line->_guid}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this);">
	         <input type="hidden" name="m" value="dPprescription" />
		       <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
		       <input type="hidden" name="del" value="0" />
		       <input type="hidden" name="prescription_line_comment_id" value="{{$line->_id}}" />
	         {{mb_field object=$line field=commentaire onblur="this.form.onsubmit();" form="editCommentaire-`$line->_guid`"
             aidesaisie="contextUserId: '`$_line_praticien_id`', resetSearchField: 0, validateOnBlur: 0"}}
	       </form>
       {{else}}
         {{mb_value object=$line field=commentaire}}
       {{/if}}
			 
			 {{if $line->category_prescription_id && $line->_can_vw_form_executant}}
         <div style="float: right">
           {{include file="../../dPprescription/templates/line/inc_vw_form_executants.tpl"}}
         </div>
       {{/if}}
     </td>
  </tr>
</tbody>
</table>