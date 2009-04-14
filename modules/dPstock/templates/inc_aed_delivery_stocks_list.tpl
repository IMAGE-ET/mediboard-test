{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th>{{tr}}CProductStockGroup-product_id{{/tr}}</th>
    <th>{{tr}}CProductStockGroup-bargraph{{/tr}}</th>
    <th></th>
  </tr>
{{foreach from=$list_stocks item=curr_stock}}
  <tbody id="delivery-{{$curr_stock->_id}}">
  {{include file="inc_aed_delivery_stock_item.tpl" stock=$curr_stock}}
  </tbody>
{{foreachelse}}
  <tr>
    <td colspan="8">{{tr}}CProductStockGroup.none{{/tr}}</td>
  </tr>
{{/foreach}}
</table>