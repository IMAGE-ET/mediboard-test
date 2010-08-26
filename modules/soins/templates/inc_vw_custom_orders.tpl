{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="tbl">
  <tr>
    <th style="width: 0.1%;">{{mb_title class=CProductDelivery field=quantity}}</th>
    <th>{{mb_title class=CProductDelivery field=date_dispensation}}</th>
    <th>{{mb_title class=CProductDelivery field=comments}}</th>
    <th style="width: 0.1%;"></th>
  </tr>
{{foreach from=$deliveries item=_delivery}}
  <tr>
    <td>{{$_delivery->quantity}}</td>
    <td>{{mb_value object=$_delivery field=date_dispensation}}</td>
    <td>{{$_delivery->comments}}</td>
    <td>
      <button type="button" class="cancel notext" onclick="removeCustomOrder(this, '{{$_delivery->_id}}')"></button>
    </td>
  </tr>
{{foreachelse}}
  <tr>
    <td colspan="4">
      {{tr}}CProductDelivery.none{{/tr}}
    </td>
  </tr>
{{/foreach}}
</table>