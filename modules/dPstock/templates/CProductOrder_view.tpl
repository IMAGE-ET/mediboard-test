{{* $Id$ *}}

{{*  
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author Fabien Ménager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
*}}

<table class="tbl">
  <tr>
    <td colspan="2">{{mb_value object=$object field=order_number}}</td>
    <td colspan="2">{{mb_value object=$object->_ref_societe field=name}}</td>
  </tr>
  <tr>
    <th>{{tr}}CProduct-name{{/tr}}</th>
    <th>{{tr}}CProduct-code{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-quantity{{/tr}}</th>
    <th>{{tr}}CProductOrderItem-unit_price{{/tr}}</th>
  </tr>
  {{foreach from=$object->_ref_order_items item=curr_item}}
  <tr>
    <td>{{mb_value object=$curr_item->_ref_reference->_ref_product field=name}}</td>
    <td>{{mb_value object=$curr_item->_ref_reference->_ref_product field=code}}</td>
    <td>{{mb_value object=$curr_item field=quantity}}</td>
    <td>{{mb_value object=$curr_item field=unit_price}}</td>
  </tr>
  {{/foreach}}
  <tr>
    <td colspan="6" id="order-{{$object->_id}}-total" style="border-top: 1px solid #666;">
      <span style="float: right;">{{tr}}Total{{/tr}} : {{mb_value object=$object field=_total}}</span>
    </td>
  </tr>
</table>