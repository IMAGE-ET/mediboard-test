{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}


{{** 
  * Permet un accès la prise en charge UPATOU, la crée si elle n'existe pas
  * 
  * @param $listPrats array|CMediusers Praticiens disponibles
  * @param $rpu CRPU Résumé de passage aux urgences
  *}}

{{assign var=sejour value=$rpu->_ref_sejour}}
{{assign var=consult value=$rpu->_ref_consult}}

<script type="text/javascript">

checkPraticien = function(oForm){
  var prat = oForm.prat_id.value;
  if(prat == ""){
    alert("Veuillez sélectionner un praticien");
    return false;
  }
  return true;
}

</script>

{{if !$consult->_id}}

	{{if !$sejour->sortie_reelle}}
		{{if $can->edit}}
		<form name="createConsult-{{$rpu->_id}}" method="post" action="?" onsubmit="return checkForm(this);">
		  <input type="hidden" name="dosql" value="do_consult_now" />
		  <input type="hidden" name="m" value="dPcabinet" />
		  <input type="hidden" name="del" value="0" />
		  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />   
		  <input type="hidden" name="patient_id" value="{{$sejour->patient_id}}" />   
		  <input type="hidden" name="accident_travail" value="{{$rpu->accident_travail}}" />
		  <select name="prat_id" class="ref notNull" style="width: 8em;">
		    <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
		    {{foreach from=$listPrats item=_prat}}
		    <option class="mediuser" style="border-color: #{{$_prat->_ref_function->color}};" value="{{$_prat->_id}}"
		      {{if $app->user_id == $_prat->_id}} selected="selected" {{/if}}>
		      {{$_prat->_view}}
		    </option>
		    {{/foreach}}
		  </select>
		  
		  <br />
		  <button type="submit" class="new" onclick="return checkPraticien(this.form)">Prendre en charge</button>
		</form>
		
		{{else}}
		  &mdash;
		{{/if}}
	{{else}}
	  {{tr}}CRPU.orientation.PSA{{/tr}}
	{{/if}}


{{else}}
  {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$consult->_ref_praticien}}
	{{if $can->edit}}
	<br />
	<a class="button search" title="Prise en charge" href="?m=dPurgences&amp;tab=edit_consultation&amp;selConsult={{$consult->_id}}">
	  Voir prise en charge
	</a>
	{{/if}}
  
{{/if}}
