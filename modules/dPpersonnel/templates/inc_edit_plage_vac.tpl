{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="editplage" action="" method="post"
      onsubmit="return onSubmitFormAjax(this, 
      { onComplete: function() {
          loadUser({{$plagevac->user_id}}, '{{$plagevac->plage_id}}') ;
          changedate('');
          editPlageVac('{{$plagevac->plage_id}}',{{$plagevac->user_id}});
      }
});">
  <input type="hidden" name="dosql" value="do_plagevac_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="user_id" value="{{$plagevac->user_id}}" />
  <input type="hidden" name="plage_id" value="{{$plagevac->plage_id}}" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <table class="form">
    <tr>
      <td colspan="2">
        <button class="new" type="button" onclick="editPlageVac('',{{$plagevac->user_id}})">
          {{tr}}CPlageVacances-title-create{{/tr}}
        </button>
        </td>
    </tr>
    {{if $plagevac->_id}}
      <tr>
        <th class = "title modify text" colspan="6">
          {{mb_include module=system template=inc_object_notes   object=$plagevac}}
          {{mb_include module=system template=inc_object_history object=$plagevac}}
          {{tr}}CPlageVacances-title-modify{{/tr}} {{$plagevac}}
        </th>
      </tr>
    {{else}}
      <tr>
        <th class = "title text" colspan="6">
         {{tr}}CPlageVacances-title-create{{/tr}} {{tr}}For{{/tr}} {{$user->_user_last_name}} {{$user->_user_first_name}}
        </th>
      </tr>
    {{/if}}
    <tr>
      <th>
        {{mb_label object=$plagevac field="libelle"}}
      </th>
      <td>
        {{mb_field object=$plagevac field="libelle"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$plagevac field="date_debut"}}
      </th>
      <td>
        {{mb_field object=$plagevac field="date_debut" form="editplage" register="true"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$plagevac field="date_fin"}}
      </th>
      <td>
        {{mb_field object=$plagevac field="date_fin" form="editplage" register="true"}}
      </td>
    </tr>

    <tr>
      <th>
        {{mb_label object=$plagevac field="replacer_id"}}
      </th>
      <td>
	      <select name="replacer_id" class="{{$plagevac->_specs.replacer_id}}">
	        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
	        {{mb_include module=mediusers template=inc_options_mediuser list=$replacers selected=$plagevac->replacer_id}}
	      </select>
      </td>
    </tr>

    <tr>
      <td colspan="6" class="button">
        <button class = "submit" type="submit">{{tr}}Save{{/tr}}</button>
        {{if $plagevac->_id}}
          <button class="trash" type="submit" onclick="confirmDeletion(this.form,{typeName:'la plage',objName:'{{$plagevac->_view|smarty:nodefaults|JSAttribute}}', ajax :true})">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>