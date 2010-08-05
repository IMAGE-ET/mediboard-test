{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td colspan="5">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$curr_item->_guid}}')">
      {{$curr_item->_ref_order_item->_view|truncate:80}}
    </strong>
  </td>
</tr>
<tr>
  <td>{{mb_value object=$curr_item field=date}}</td>
  {{if $dPconfig.dPstock.CProductStockGroup.unit_order}}
    <td style="text-align: right;">{{mb_value object=$curr_item field=_unit_quantity}}</td>
    <td>{{mb_value object=$curr_item->_ref_order_item->_ref_reference->_ref_product field=_unit_title}}</td>
  {{else}}
    <td style="text-align: right;">{{mb_value object=$curr_item field=quantity}}</td>
  {{/if}}
  <td>{{mb_value object=$curr_item field=code}}</td>
  <td>{{mb_value object=$curr_item field=lapsing_date}}</td>
</tr>