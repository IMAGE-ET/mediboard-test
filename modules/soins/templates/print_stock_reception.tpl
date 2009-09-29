{{* $Id: inc_restockages_service_list.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="print tbl">
  <tr>
    <th>{{*tr}}CProductDelivery-service_id{{/tr*}}Pour</th>
    <th>{{tr}}CProduct{{/tr}}</th>
    <th>{{tr}}CProductDelivery-date_dispensation{{/tr}}</th>
    <th>{{tr}}CProductDelivery-quantity{{/tr}}</th>
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th></th>
  </tr>
  {{foreach from=$deliveries item=curr_delivery}}
    <tr>
      <td>
        {{if $curr_delivery->patient_id}}
          {{$curr_delivery->_ref_patient->_view}}
        {{else}}
          {{$curr_delivery->_ref_service->_view}}
        {{/if}}
      </td>
      <td>{{$curr_delivery->_ref_stock->_view}}</td>
      <td>{{mb_value object=$curr_delivery field=date_dispensation}}</td>
      <td>{{mb_value object=$curr_delivery field=quantity}}</td>
      <td>{{mb_value object=$curr_delivery->_ref_stock->_ref_product field=_unit_title}}</td>
      <td>
        {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
          {{if !$trace->date_reception}}
            {{mb_value object=$trace field=quantity}} 
            [{{mb_value object=$trace field=code}}]
            {{mb_value object=$trace field=date_delivery}}
          {{else}}
            <b>{{mb_value object=$trace field=quantity}}</b>
          {{/if}}
          <br />
        {{foreachelse}}
          Pas encore délivré
        {{/foreach}}
      </td>
    </tr>
  {{foreachelse}}
  <tr>
    <td colspan="10">{{tr}}CProductDelivery.global.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>