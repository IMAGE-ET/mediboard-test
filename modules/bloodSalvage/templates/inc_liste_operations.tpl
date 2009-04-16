{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{** 
  * @param $urgence bool Urgence mode
  * @param $vueReduite bool Affichage compact
  * @param $operations array|COperation interventions à afficher
  *}}

<!-- Entêtes -->
<tr>
  {{if $urgence && $salle}}
  <th>Praticien</th>
  {{else}}
  <th>Heure</th>
  {{/if}}
  <th>Patient</th>
  {{if !$vueReduite}}
  <th>Actes</th>
  <th>Coté</th>
  <th>Durée</th>
  {{/if}}
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
    <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage&amp;op={{$_operation->_id}}" title="Coder l'intervention">
		  {{if $urgence && $salle}}
		  {{$_operation->_ref_chir->_view}}
		  {{else}}
	      {{if $_operation->time_operation != "00:00:00"}}
	        {{$_operation->time_operation|date_format:$dPconfig.time}}
	      {{else}}
	        NP
	      {{/if}}
	    {{/if}}
    </a>
  </td>
 
  {{if $_operation->_deplacee}}
  <td class="text" colspan="5">
    <div class="warning">
    	{{$_operation->_ref_patient->_view}}
    	<br />
	    Intervention déplacée vers {{$_operation->_ref_salle->_view}}
    </div>
  </td>
  
  {{else}}
  <td class="text">
	  {{if $vueReduite}}
	    <button style="float:right" class="print notext" onclick="printFeuilleBloc({{$_operation->_id}})">{{tr}}Imprimer{{/tr}}</button>
	  {{/if}}
    <a href="?m=bloodSalvage&amp;tab=vw_bloodSalvage&amp;salle={{$salle->_id}}&amp;op={{$_operation->_id}}" title="Coder l'intervention">
      {{$_operation->_ref_patient->_view}}
    </a>
  </td>
  
	{{if !$vueReduite}}
  <td>
		{{mb_ternary var=direction test=$urgence value=vw_edit_urgence other=vw_edit_planning}}
    <a href="?m=dPplanningOp&amp;tab={{$direction}}&amp;operation_id={{$_operation->_id}}" title="Modifier l'intervention">
      {{foreach from=$_operation->_ext_codes_ccam item=curr_code}}
      {{$curr_code->code}}<br />
      {{/foreach}}
    </a>
  </td>
  <td>{{tr}}COperation.cote.{{$_operation->cote}}{{/tr}}</td>
  <td>{{$_operation->temp_operation|date_format:$dPconfig.time}}</td>
  {{/if}}
  {{/if}}
</tr>

{{if $dPconfig.dPsalleOp.COperation.modif_salle && !$_operation->_deplacee}}
<tr>
  <td colspan="5">
    <form name="changeSalle{{$_operation->_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="dosql" value="do_planning_aed" />
      <input type="hidden" name="m" value="dPplanningOp" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="operation_id" value="{{$_operation->_id}}" />
      <select name="salle_id" onchange="this.form.submit()">
        <option value="">&mdash; {{tr}}CSalle.none{{/tr}}</option>
        {{foreach from=$listBlocs item=curr_bloc}}
        <optgroup label="{{$curr_bloc->nom}}">
          {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
          <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $_operation->_id}}selected="selected"{{/if}}>
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
