{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<hr />

<form name="addFunction" action="" method="post" onsubmit="return onSubmitFormAjax(this, { onComplete: refreshListElement.curry('','{{$category_id}}','',true) });">
  <input type="hidden" name="dosql" value="do_function_category_prescription_aed" />
  <input type="hidden" name="m" value="dPprescription" />
	<input type="hidden" name="del" value="0" />
  
  <input type="hidden" name="function_category_prescription_id" value="{{$executant->_id}}" />
  <input type="hidden" name="category_prescription_id" value="{{$category_id}}" />
	<input type="hidden" name="callback" value="refreshFormExecutantFunction" />
  
        
	<table class="form">
	  <tr>
	  	{{if $executant->_id}}
			  <th class="title modify" colspan="2">Modification d'un executant</th>
      {{else}}
	      <th class="title" colspan="2">Création d'un executant</th>
		  {{/if}}
		</tr>	
	  <tr>
	  	<th>
	  		{{mb_label object=$executant field="function_id"}}
	  	</th>
	    <td style="width: 20%;">
	      <select name="function_id">
          {{foreach from=$functions item=function}}
            <option value="{{$function->_id}}" {{if $function->_id == $executant->function_id}}selected="selected"{{/if}}class="mediuser" style="border-left-color: #{{$function->color}};">{{$function->_view}}</option>
          {{/foreach}}
	        </select>
	    </td>
	  </tr>
		<tr>
			<td class="button" colspan="2">
	      <button type="submit" class="new">{{tr}}Save{{/tr}}</button>
				{{if $executant->_id}}
        <button class="trash" type="button" name="delete" onclick="confirmDeletion(this.form,{ ajax: true, typeName:'l\'executant',objName:'{{$executant->_ref_function->_view|smarty:nodefaults|JSAttribute}}'})">
          {{tr}}Delete{{/tr}}
        </button>
				{{/if}}
  		</td>
		</tr>
	</table>
</form>