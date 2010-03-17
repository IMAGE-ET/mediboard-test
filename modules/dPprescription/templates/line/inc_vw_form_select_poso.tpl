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
  </form>
	<br />
{{else}}
  {{if $line->no_poso}}
    {{$line->_ref_posologie->_view}}
  {{else}}
    <!-- Aucune posologie sélectionnée -->
  {{/if}}
{{/if}}