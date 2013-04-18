{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

<form name="editCategory" action="?m={{$m}}" method="post" onsubmit="return Keeper.submitCategory(this, '{{$keeper->_id}}')">
  <input type="hidden" name="password_keeper_id" value="{{$keeper->_id}}" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$category}}
  {{mb_class object=$category}}

  <table class="form">
    {{mb_include module=system template=inc_form_table_header object=$category colspan=2}}
    <tr>
      <th>{{mb_label object=$category field="category_name"}}</th>
      <td>{{mb_field object=$category field="category_name"}}</td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button class="submit" type="submit">{{tr}}Validate{{/tr}}</button>
        <button
          class="trash" type="button" onclick="confirmDeletion(
          this.form,
          {ajax:true, typeName:'la catégorie',objName:'{{$category->_view|smarty:nodefaults|JSAttribute}}'},
          {
          onComplete : function() { Keeper.showListCategory('{{$keeper->_id}}'); },
          check: function() { return true; }
          })">{{tr}}Delete{{/tr}}</button>
    </tr>
  </table>
</form>

{{if $category->_id}}
  <div id="vw_list_password"></div>
{{/if}}