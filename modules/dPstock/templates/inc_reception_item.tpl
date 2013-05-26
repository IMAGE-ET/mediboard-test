{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td rowspan="2">
    {{if !$reception->locked}}
      <form name="delete-reception_item-{{$curr_item->_id}}" method="post" action="?">
        <input type="hidden" name="m" value="dPstock" />
        <input type="hidden" name="dosql" value="do_order_item_reception_aed" />
        <input type="hidden" name="del" value="0" />
        {{mb_key object=$curr_item}}
        <button type="button" class="trash notext" onclick="confirmDeletion(this.form,{typeName:'cette réception',objName:'', ajax: 1 }, {onComplete: refreshReception.curry({{$reception->_id}})})">
          {{tr}}Delete{{/tr}}
        </button>
      </form>
    {{/if}}
  </td>
  <td colspan="6">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$curr_item->_guid}}')">
      {{$curr_item->_ref_order_item->_view|truncate:80}}
    </strong>
  </td>
</tr>
<tr>
  <td>{{mb_value object=$curr_item field=date}}</td>
  <td style="text-align: right;">{{mb_value object=$curr_item field=quantity}}</td>
  <td>{{mb_value object=$curr_item->_ref_order_item->_ref_reference->_ref_product field=_unit_title}}</td>
  <td>{{mb_value object=$curr_item field=code}}</td>
  <td>{{mb_value object=$curr_item field=lapsing_date}}</td>
</tr>