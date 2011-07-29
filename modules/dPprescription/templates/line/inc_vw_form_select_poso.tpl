{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $line->_can_modify_poso}}
 {{if $line->_most_used_poso|@count}}
	<form action="?" method="post" name="editLine-{{$line->_id}}" onsubmit="return checkForm(this);">
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="del" value="0" />
    {{mb_key object=$line}}
		
		{{if $line instanceof CPrescriptionLineMedicament}}
			<input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
			<input type="hidden" name="_code_cip" value="{{$line->code_cip}}" />
			{{assign var=filter_value value=$line->code_cip}}
		{{else}}
		  <input type="hidden" name="dosql" value="do_prescription_line_element_aed" />
			<input type="hidden" name="_element_prescription_id" value="{{$line->element_prescription_id}}" />
			{{assign var=filter_value value=$line->element_prescription_id}}
    {{/if}}
		<input type="hidden" name="_delete_prises" value="0" />

   	<select name="_line_id_for_poso" style="width: 160px;" onchange="submitPoso(this.form, '{{$line->_id}}', '{{$line->_class}}', '{{$typeDate}}');">
		  <option value="">Posologies les plus utilisées</option>
		  {{foreach from=$line->_most_used_poso item=_poso_view key=_line_poso_id}}
		    <option value="{{$_line_poso_id}}">{{$_poso_view.view}}</option>
		  {{/foreach}}
	  </select>
		
		<button type="button" class="search" onclick="Prescription.viewStatPoso('{{$line->_class}}', '{{$filter_value}}', '{{if $prescription->object_id}}{{$line->praticien_id}}{{else}}{{$prescription->praticien_id}}{{/if}}')">
      Stats
    </button>
  </form>
	<br />
	{{/if}}
{{elseif $line instanceof CPrescriptionLineMedicament}}
  {{if $line->no_poso}}
    {{$line->_ref_posologie->_view}}
  {{/if}}
{{/if}}