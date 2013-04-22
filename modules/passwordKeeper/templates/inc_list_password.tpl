{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

<table class="tbl" id="passwordList">
  <tr>
    <button type="button" class="new" onclick="Keeper.showPasswordEntry('0', '{{$password->category_id}}')">{{tr}}CPasswordEntry-title-create{{/tr}}</button>
  </tr>
  <tr>
    <th class="title" colspan="3">{{tr}}CPasswordEntry{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}CPasswordEntry-password_description{{/tr}}</th>
    <th>{{tr}}CPasswordEntry-password{{/tr}}</th>
    <th>{{tr}}CPasswordEntry-password_comments{{/tr}}</th>
  </tr>
  {{foreach from=$passwords item=_password}}
    <tr {{if $password_id == $_password->_id}}class='selected'{{/if}}>
      <td>
        <a href="#1" onclick="Keeper.showPasswordEntry('{{$_password->_id}}', '{{$password->category_id}}', this)" title="{{tr}}CPasswordEntry-title-modify{{/tr}}">
          {{mb_value object=$_password field="password_description"}}
        </a>
      </td>
      <td>
        <button class="lookup" type="button" onclick="Keeper.revealPasswordEntry('{{$_password->_id}}')">{{tr}}CPasswordEntry-reveal{{/tr}}</button>
      </td>
      <td>
        {{mb_value object=$_password field="password_comments"}}
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty" colspan="3">{{tr}}CPasswordEntry.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

<div id="vw_edit_password"></div>

<div id="vw_reveal_password"></div>