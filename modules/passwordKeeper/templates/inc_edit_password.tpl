{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

<form name="editPassword" action="?m={{$m}}" method="post" onsubmit="return Keeper.submitPasswordEntry(this, '{{$category->_id}}')">
  <input type="hidden" name="category_id" value="{{$category->_id}}" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$password}}
  {{mb_class object=$password}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$password colspan=3}}
    <tr>
      <th>{{mb_label object=$password field="password_description"}}</th>
      <td>{{mb_field object=$password field="password_description"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$password field="password"}}</th>
      <td>{{mb_field object=$password field="password"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$password field="password_comments"}}</th>
      <td>{{mb_field object=$password field="password_comments"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="3">
        <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
        <button
          class="trash" type="button" onclick="confirmDeletion(
            this.form,
            {ajax:true, typeName:'le mot de passe',objName:'{{$password->_view|smarty:nodefaults|JSAttribute}}'},
            {
              onComplete : function() { Keeper.showListPasswordEntry('{{$category->_id}}'); },
              check: function() { return true; }
            })">{{tr}}Delete{{/tr}}</button>
    </tr>
  </table>
</form>