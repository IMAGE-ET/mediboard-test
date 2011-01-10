{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

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
     <td style="width: 5%; text-align: center;" class="text">
			 {{if $line->_can_delete_line}}
       <form name="delLineComment-{{$line->_id}}" action="" method="post">
         <input type="hidden" name="m" value="dPprescription" />
         <input type="hidden" name="dosql" value="do_prescription_line_comment_aed" />
         <input type="hidden" name="del" value="1" />
         <input type="hidden" name="prescription_line_comment_id" value="{{$line->_id}}" />
         <button type="button" class="trash notext" onclick="return onSubmitFormAjax(this.form, { onComplete: function() { Prescription.reload('{{$prescription->_id}}',null,'{{$div_refresh}}','{{$mode_protocole}}','{{$mode_pharma}}') } } );">
           {{tr}}Delete{{/tr}}
         </button>
        </form>
      {{/if}}
		</td>
		 
		<td style="width: 75%;" class="text">
		  <strong>
        {{mb_value object=$line field="commentaire"}}
      </strong>
		</td>	 
		
    <td style="width: 10%;"> 
      {{if ($line->category_prescription_id || $prescription->type != "sejour") && $line->category_prescription_id}}
         {{if $line->executant_prescription_line_id || $line->user_executant_id}}
				   {{$line->_ref_executant->_view}}
				 {{else}}
				   {{tr}}None{{/tr}}
				 {{/if}}
     {{/if}}
		 </td>
		 
		 <td style="width: 10%;">  
		  <button style="float: right" class="edit notext" onclick="Prescription.reloadLine('{{$line->_guid}}','{{$mode_protocole}}','{{$mode_pharma}}','{{$operation_id}}');"></button>
      {{if !$line->_protocole}}
        <div class="mediuser" style="border-color: #{{$line->_ref_praticien->_ref_function->color}};">
            {{if @$modules.messagerie}}
            <a class="action" href="#nothing" onclick="MbMail.create({{$line->_ref_praticien->_id}}, '{{$line->_view}}')">
              <img src="images/icons/mbmail.png" title="Envoyer un message" />
            </a>
            {{/if}}
            {{if $line->signee}}
              <img src="images/icons/tick.png" title="Ligne signée par le praticien" />
            {{else}}
              <img src="images/icons/cross.png" title="Ligne non signée par le praticien" />
            {{/if}}
            <label title="{{$line->_ref_praticien->_view}}">{{$line->_ref_praticien->_shortview}}</label>
          </div>
        {{else}}
          - 
        {{/if}}
     </td>
  </tr>
</tbody>