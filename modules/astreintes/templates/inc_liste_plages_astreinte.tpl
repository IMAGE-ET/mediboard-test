{{* $Id:*}}

{{*
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}
<table class="tbl">
  <tr>
    <th colspan="10"class="title">Plages pour {{mb_value object=$user field="_user_last_name"}} {{mb_value object=$user field="_user_first_name"}}</th>
  </tr>
  <tr>
    <th>{{mb_title class=CPlageAstreinte field=libelle}}</th>
    <th>{{tr}}Dates{{/tr}}</th>
  </tr>
  {{foreach from=$plages_astreinte item=_plageastreinte}}
    <tr id="p{{$_plageastreinte->_id}}" {{if $plage_id == $_plageastreinte->_id}} class="selected" {{/if}}>
      <td>
        <a href="#Edit-{{$_plageastreinte->_guid}}"
           onclick="PlageAstreinte.edit('{{$_plageastreinte->_id}}','{{$user->_id}}')">
          <span onmouseover="ObjectTooltip.createEx(this, '{{$_plageastreinte->_guid}}')">
          {{if $_plageastreinte->libelle}}
            {{mb_value object=$_plageastreinte field="libelle"}}
          {{else}}
            {{tr}}CPlageAstreinte.noLibelle{{/tr}}
          {{/if}}
          </span>
        </a>
      </td>
      <td>
        {{mb_include module=system template=inc_interval_date from=$_plageastreinte->date_debut to=$_plageastreinte->date_fin}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="3" class="empty">{{tr}}CPlageAstreinte.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>