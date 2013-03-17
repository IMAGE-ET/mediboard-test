{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage dPplanningOp
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<th>{{mb_label object=$object field=adresse_par_prat_id}}</th>
<td colspan="3">
  <select name="_correspondants_medicaux" style="width: 15em;" onchange="$V(this.form.adresse_par_prat_id, $V(this)); $('_adresse_par_prat').hide()">
    <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
    {{foreach from=$correspondantsMedicaux key=type_correspondant item=_correspondant}}
      {{if $type_correspondant == "traitant"}}
        <option value="{{$_correspondant->_id}}" {{if $_correspondant->_id == $object->adresse_par_prat_id}}selected="selected"{{/if}}>
          Trait : {{$_correspondant->nom}}
        </option>  
      {{else}}
        {{foreach from=$_correspondant item=medecin_corres}}
          <option value="{{$medecin_corres->_id}}" {{if $medecin_corres->_id == $object->adresse_par_prat_id}}selected="selected"{{/if}}>
            Corresp : {{$medecin_corres->nom}}
          </option> 
        {{/foreach}}
      {{/if}}
    {{/foreach}}
  </select>
  <button class="search" type="button" onclick="Medecin.edit()">Autres</button>
</td>