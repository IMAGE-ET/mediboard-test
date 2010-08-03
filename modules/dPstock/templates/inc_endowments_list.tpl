{{* $Id: inc_products_list.tpl 8067 2010-02-12 10:31:41Z phenxdesign $ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 8067 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include module=system template=inc_pagination change_page="changePageEndowment" 
    total=$total current=$start step=20}}

<table class="tbl">
  <tr>
    <th>{{mb_title class=CProductEndowment field=name}}</th>
    <th>{{mb_title class=CProductEndowment field=service_id}}</th>
    <th>{{tr}}CProductEndowment-back-endowment_items{{/tr}}</th>
  </tr>
  {{foreach from=$list item=_endowment}}
    <tbody class="hoverable">
    <tr {{if $_endowment->_id == $endowment->_id}}class="selected"{{/if}}>
      <td style="font-weight: bold;">
        <a href="#1" onclick="return loadEndowment({{$_endowment->_id}})">
          {{mb_value object=$_endowment field=name}}
        </a>
      </td>
      <td>{{mb_value object=$_endowment field=service_id}}</td>
      <td>
        {{$_endowment->_count.endowment_items}}
      </td>
    </tr>
    </tbody>
  {{foreachelse}}
    <tr>
      <td colspan="6">{{tr}}CProductEndowment.none{{/tr}}</td>
    </tr>
  {{/foreach}}
</table>