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
          changedate('');
          Control.Modal.close();
      }
});">
  {{mb_key object=$plageastreinte}}
  <input type="hidden" name="dosql" value="do_plageastreinte_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="callback" value="PlageAstreinte.edit" />
  <table class="form">
    {{if $plageastreinte->_id}}
      <tr>
        <th class = "title modify text" colspan="6">
          {{mb_include module=system template=inc_object_notes   object=$plageastreinte}}
          {{mb_include module=system template=inc_object_history object=$plageastreinte}}
          {{tr}}CPlageAstreinte-title-modify{{/tr}} {{$plageastreinte}}
        </th>
      </tr>
      <tr>
        <th class = "title text" colspan="6">
         {{tr}}CPlageAstreinte-title-modify{{/tr}} {{if $user}}{{tr}}For{{/tr}} {{$user->_user_last_name}} {{$user->_user_first_name}}{{/if}}
        </th>
      </tr>
    {{else}}
      <tr>
        <th class = "title text" colspan="6">
         {{tr}}CPlageAstreinte-title-create{{/tr}}
        </th>
      </tr>
    {{/if}}
      <th>
        {{mb_label object=$plageastreinte field="user_id"}}
      </th>
      <td>
        <select name="user_id">
           <option value="">{{tr}}CMediusers.all{{/tr}}</option>
           {{mb_include module=mediusers template=inc_options_mediuser list=$users selected=$user->_id}}
         </select>
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$plageastreinte field="libelle"}}
      </th>
      <td>
        {{mb_field object=$plageastreinte field="libelle"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$plageastreinte field="date_debut"}}
      </th>
      <td>
        {{mb_field object=$plageastreinte field="date_debut" form="editplage" register="true"}}
      </td>
    </tr>
    <tr>
      <th>
        {{mb_label object=$plageastreinte field="date_fin"}}
      </th>
      <td>
        {{mb_field object=$plageastreinte field="date_fin" form="editplage" register="true"}}
      </td>
    </tr>
    <tr>
      <td colspan="6" class="button">
        <button class="submit" type="button" onclick="this.form.onsubmit()">{{tr}}Save{{/tr}}</button>
        {{if $plageastreinte->_id}}
          <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la plage',objName:'{{$plageastreinte->_view|smarty:nodefaults|JSAttribute}}', ajax :true})">{{tr}}Delete{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>
