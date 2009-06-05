{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{** 
  * @param $urgence bool Urgence mode
  * @param $vueReduite bool Affichage compact
  * @param $operations array|COperation interventions ? afficher
  *}}

<!-- Entêtes -->
<tr>
  {{if $urgence && $salle}}
  <th>Praticien</th>
  {{else}}
  <th>Heure</th>
  {{/if}}
  <th>Patient</th>
</tr>

{{foreach from=$operations item=_operation}}
{{if $dPconfig.dPsalleOp.COperation.modif_salle}}
  {{assign var="rowspan" value=2}}
{{else}}
  {{assign var="rowspan" value=1}}
{{/if}}
<tbody class="hoverable">
<tr {{if $_operation->_id == $operation_id}}class="selected"{{/if}}>
  {{if $_operation->_deplacee}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#ccf">
  {{elseif $_operation->entree_salle && $_operation->sortie_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-image:url(images/icons/ray.gif); background-repeat:repeat;">
  {{elseif $_operation->entree_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#cfc">
  {{elseif $_operation->sortie_salle}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#fcc">
  {{elseif $_operation->entree_bloc}}
  <td class="text" rowspan="{{$rowspan}}" style="background-color:#ffa">
  {{else}}
  <td class="text" rowspan="{{$rowspan}}">
  {{/if}}
		  {{if $urgence && $salle}}
		  {{$_operation->_ref_chir->_view}}
		  {{else}}
	      {{if $_operation->time_operation != "00:00:00"}}
	        {{$_operation->time_operation|date_format:$dPconfig.time}}
	      {{else}}
	        NP
	      {{/if}}
	    {{/if}}
  </td>
 
  {{if $_operation->_deplacee}}
  <td class="text" colspan="5">
    <div class="warning">
    	{{$_operation->_ref_patient->_view}}
    	<br />
	    Intervention dÈplacÈe vers {{$_operation->_ref_salle->_view}}
    </div>
  </td>
  
  {{else}}
  <td class="text">
    {{if $dPconfig.dPbloc.CPlageOp.chambre_operation == 0}}
      {{$_operation->_ref_patient->_view}}
    {{else}}
      <table style="border: none; width: 100%;">
        <tr>
          <td>
          	 {{$_operation->_ref_patient->_view}}
          </td>
          {{if $_operation->_ref_affectation && $_operation->_ref_affectation->_ref_lit->_id}}
            <td style="text-align: center; font-size: 0.8em; width: 1%; white-space: nowrap;">
              {{$_operation->_ref_affectation->_ref_lit->_ref_chambre->_ref_service->_view}}<br />
              {{$_operation->_ref_affectation->_ref_lit->_view}}
            </td>
          {{/if}}
  	  	</tr>
      </table>
      {{$_operation->libelle}}
    {{/if}}
  </td>
  {{/if}}
</tr>

{{if $dPconfig.dPsalleOp.COperation.modif_salle && !$_operation->_deplacee && !($tab == "vw_suivi_salles" && $m == "dPsalleOp")}}
<tr>
  <td colspan="5">
    <form name="changeSalle{{$_operation->_id}}" action="?m={{$m}}" method="post">
    <input type="hidden" name="dosql" value="do_planning_aed" />
    <input type="hidden" name="m" value="dPplanningOp" />
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
    <select name="salle_id" onchange="this.form.submit();">

      {{foreach from=$listBlocs item=curr_bloc}}
      <optgroup label="{{$curr_bloc->nom}}">
        {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
        <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $_operation->salle_id}}selected="selected"{{/if}}>
          {{$curr_salle->nom}}
        </option>
        {{foreachelse}}
        <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
        {{/foreach}}
      </optgroup>
      {{/foreach}}
    </select>
    </form>
  </td>
</tr>
{{/if}}
</tbody>
{{/foreach}}
