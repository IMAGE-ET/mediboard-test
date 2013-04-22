{{*
 * $Id$
 *
 * @category Password Keeper
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org*}}

<table class="tbl" id="categoryList">
  <tr>
    <button type="button" class="new" onclick="Keeper.showCategory('0', '{{$category->password_keeper_id}}')">{{tr}}CPasswordCategory-title-create{{/tr}}</button>
  </tr>
  <tr>
    <th class="title">{{tr}}CPasswordCategory{{/tr}}</th>
  </tr>
  <tr>
    <th>{{tr}}CPasswordCategory-category_name{{/tr}}</th>
  </tr>
  {{foreach from=$categories item=_category}}
    <tr {{if $category_id == $_category->_id}}class='selected'{{/if}}>
      <td>
        <a href="#1" onclick="Keeper.showCategory('{{$_category->_id}}', '{{$_category->password_keeper_id}}', this)" title="{{tr}}CPasswordCategory-title-modify{{/tr}}">
          {{mb_value object=$_category field="category_name"}}
        </a>
      </td>
    </tr>
    {{foreachelse}}
    <tr>
      <td class="empty">{{tr}}CPasswordCategory.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>

<div id="vw_edit_category"></div>