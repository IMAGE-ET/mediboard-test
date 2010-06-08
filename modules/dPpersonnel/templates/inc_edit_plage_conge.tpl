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
          loadUser({{$plageconge->user_id}}, '{{$plageconge->plage_id}}') ;
          changedate('');
      }
});">
  <input type="hidden" name="dosql" value="do_plageconge_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="user_id" value="{{$plageconge->user_id}}" />
  <input type="hidden" name="plage_id" value="{{$plageconge->plage_id}}" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="callback" value="editPlageCongeCallback" />
  <table class="form">
    <tr>
      <td colspan="2">
        <button class="new" type="button" onclick="editPlageConge('',{{$plageconge->user_id}})">
          {{tr}}CPlageConge-title-create{{/tr}}
        </button>
        </td>
    </tr>
    {{if $plageconge->_id}}
      <tr>
        <th class = "title modify text" colspan="6">
          {{mb_include module=system template=inc_object_notes   object=$plageconge}}
          {{mb_include module=system template=inc_object_history object=$plageconge}}
          {{tr}}CPlageConge-title-modify{{/tr}} {{$plageconge}}
        </th>
      </tr>
    {{else}}
      <tr>
        <th class = "title text" colspan="6">
         {{tr}}CPlageConge-title-create{{/tr}} {{tr}}For{{/tr}} {{$user->_user_last_name}} {{$user->_user_first_name}}
        </th>
      </tr>
    {{/if}}
    <tr>
      <th>
        {{mb_label object=$plageconge field="libelle"}}
      </th>
      <td>
        {{mb_field object=$plageconge field="libelle"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$plageconge field="date_debut"}}
      </th>
      <td>
        {{mb_field object=$plageconge field="date_debut" form="editplage" register="true"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$plageconge field="date_fin"}}
      </th>
      <td>
        {{mb_field object=$plageconge field="date_fin" form="editplage" register="true"}}
      </td>
    </tr>

    <tr>
      <th>
        {{mb_label object=$plageconge field="replacer_id"}}
      </th>
      <td>
	      <select name="replacer_id" class="{{$plageconge->_specs.replacer_id}}">
	        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
	        {{mb_include module=mediusers template=inc_options_mediuser list=$replacers selected=$plageconge->replacer_id}}
	      </select>
      </td>
    </tr>

    <tr>
      <td colspan="6" class="button">
        <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
        {{if $plageconge->_id}}
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la plage',objName:'{{$plageconge->_view|smarty:nodefaults|JSAttribute}}', ajax :true})">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>