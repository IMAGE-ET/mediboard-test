{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if $line->_can_modify_poso}}
	<form action="?m=dPprescription" method="post" name="editLine-{{$line->_id}}" onsubmit="return checkForm(this);">
	  <input type="hidden" name="m" value="dPprescription" />
	  <input type="hidden" name="dosql" value="do_prescription_line_medicament_aed" />
	  <input type="hidden" name="prescription_line_medicament_id" value="{{$line->_id}}"/>
	  <input type="hidden" name="del" value="0" />
	  <input type="hidden" name="_code_cip" value="{{$line->_ref_produit->code_cip}}" />
	  <input type="hidden" name="_delete_prises" value="0" />
	
    {{if $dPconfig.dPprescription.CPrisePosologie.show_poso_bcb || !$prescription->object_id}}
		  {{assign var=posologies value=$line->_ref_produit->_ref_posologies}}
		  <select name="no_poso" {{if $dPconfig.dPprescription.CPrisePosologie.select_poso_bcb}}onchange="testPharma({{$line->_id}}); submitPoso(this.form, '{{$line->_id}}');"{{/if}} style="width: 230px;">
		    <option value="">&mdash; Posologies automatiques</option>
		    {{foreach from=$line->_ref_produit->_ref_posologies item=curr_poso}}
		    <option value="{{$curr_poso->code_posologie}}"
		      {{if $curr_poso->code_posologie == $line->no_poso}}selected="selected"{{/if}}>
		      {{$curr_poso->_view}}
		    </option>
		    {{/foreach}}
		  </select>  
    {{else}}
    	<select name="_line_id_for_poso" style="width: 160px;"
		        onchange="submitPoso(this.form, '{{$line->_id}}');">
			  <option value="">Posologies les plus utilisées</option>
			  {{foreach from=$line->_most_used_poso item=_poso_view key=_line_poso_id}}
			    <option value="{{$_line_poso_id}}">{{$_poso_view.view}}</option>
			  {{/foreach}}
		  </select>
		  <button type="button" class="search" onclick="Prescription.viewStatPoso('{{$line->code_cip}}','{{if $prescription->object_id}}{{$line->praticien_id}}{{else}}{{$prescription->praticien_id}}{{/if}}')">
        Stats
      </button>
	  {{/if}}
  </form>
	<br />
{{else}}
  {{if $line->no_poso}}
    {{$line->_ref_posologie->_view}}
  {{else}}
    Aucune posologie sélectionnée
  {{/if}}
{{/if}}