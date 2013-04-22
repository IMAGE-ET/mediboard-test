{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

<table class="tbl" id="keeperList">
  <tr>
    <th class="title" colspan="2">{{tr}}CPasswordKeeper{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}CPasswordKeeper-keeper_name{{/tr}}</th>
    <th>{{tr}}CPasswordKeeper-is_public{{/tr}}</th>
  </tr>
  {{foreach from=$keepers item=_keeper}}
    {{assign var=id value=$_keeper->_id}}
    <tr {{if $password_keeper_id == $_keeper->_id}}class='selected'{{/if}}>
      <td>
        <a href="#1" onclick="Keeper.showKeeper('{{$_keeper->_id}}', this)" title="{{tr}}CPasswordKeeper-title-modify{{/tr}}">
          {{mb_value object=$_keeper field="keeper_name"}}
          {{if $counts.$id == 0}}
            <span class="text compact">({{tr}}CPasswordCategory.none{{/tr}})</span>
          {{else}}
            <span class="text compact">({{$counts.$id}} {{tr}}CPasswordCategory{{/tr}})</span>
          {{/if}}
        </a>
      </td>
      <td>
        {{mb_value object=$_keeper field="is_public"}}
      </td>
    </tr>
  {{/foreach}}
</table>