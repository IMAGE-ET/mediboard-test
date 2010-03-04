{{* $Id: inc_products_list.tpl 8067 2010-02-12 10:31:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8067 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePage" 
    total=$total current=$start step=20}}

<table class="tbl">
  <tr>
    <th>{{mb_title class=CProductSelection field=name}}</th>
    <th>{{tr}}CProductSelection-back-selection_items{{/tr}}</th>
  </tr>
  {{foreach from=$list item=_selection}}
    <tbody class="hoverable">
    <tr {{if $_selection->_id == $selection->_id}}class="selected"{{/if}}>
      <td style="font-weight: bold;">
        <a href="#1" onclick="return loadSelection({{$_selection->_id}})">
          {{mb_value object=$_selection field=name}}
        </a>
      </td>
      <td>
        {{$_selection->_count.selection_items}}
      </td>
    </tr>
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CProductSelection.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>