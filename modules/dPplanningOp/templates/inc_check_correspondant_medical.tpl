{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPlanningOp
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<th>{{mb_label object=$sejour field=adresse_par_prat_id}}</th>
<td colspan="3">
  <select name="_correspondants_medicaux" onchange="$V(this.form.adresse_par_prat_id, $V(this)); $('_adresse_par_prat').hide()">
    <option value="">&mdash; Choisir un correspondant</option>
    {{foreach from=$correspondantsMedicaux key=type_correspondant item=curr_correspondant}}
      {{if $type_correspondant == "traitant"}}
        <option value="{{$curr_correspondant->_id}}" {{if $curr_correspondant->_id == $sejour->adresse_par_prat_id}}selected="selected"{{/if}}>
          Trait : {{$curr_correspondant->nom}}
        </option>  
      {{else}}
        {{foreach from=$curr_correspondant item=medecin_corres}}
          <option value="{{$medecin_corres->_id}}" {{if $medecin_corres->_id == $sejour->adresse_par_prat_id}}selected="selected"{{/if}}>
            Corresp : {{$medecin_corres->nom}}
          </option> 
        {{/foreach}}
      {{/if}}
    {{/foreach}}
  </select>
  <button class="search" type="button" onclick="Medecin.edit()">Autres</button>
</td>