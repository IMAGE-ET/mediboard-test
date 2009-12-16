{{* $Id: inc_vw_line_delivrance.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=id value=$curr_delivery->_id}}

<tr>
  <td>
    {{if $curr_delivery->patient_id}}
      {{$curr_delivery->_ref_patient->_view}}
    {{else}}
      {{$curr_delivery->_ref_service->_view}}
    {{/if}}
  </td>
  <td>
    <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_guid}}')">
      {{$curr_delivery->_ref_stock->_view}}
    </span>
  </td>
  <td class="text">{{$curr_delivery->comments}}</td>
  {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
  <td>
    <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_id={{$curr_delivery->_ref_stock->_id}}" title="{{tr}}CProductStockGroup-title-modify{{/tr}}">
      {{mb_value object=$curr_delivery->_ref_stock field=quantity}}
    </a>
  </td>
  {{/if}}
  <td style="text-align: center;">{{mb_value object=$curr_delivery field=quantity}}</td>
  
  {{* 
  {{if !$dPconfig.dPstock.CProductStockService.infinite_quantity}}
    <td style="text-align: center;">
      {{assign var=stock value=$curr_delivery->_ref_stock}}
      <a href="?m=dPstock&amp;tab=vw_idx_stock_service&amp;stock_service_id={{$stock->_id}}" title="{{tr}}CProductStockService-title-modify{{/tr}}">
        {{$stock->quantity}}
      </a>
    </td>
  {{/if}}
  *}}
  
  <td style="text-align: center;">
    {{$curr_delivery->_ref_stock->_ref_product->_unit_title}}
  </td>
  <td>
    <form name="dispensation-validate-{{$curr_delivery->_id}}" onsubmit="return false" action="?" method="post" {{if $curr_delivery->isDelivered()}}style="opacity: 0.4;"{{/if}}>
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      <input type="hidden" name="date_dispensation" value="now" />
      <input type="hidden" name="order" value="0" />
      {{mb_field object=$curr_delivery field=quantity increment=1 form=dispensation-validate-$id size=3 value=$curr_delivery->quantity-$curr_delivery->countDelivered()}}
      <button type="submit" class="tick notext" onclick="onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Dispenser">Dispenser</button>
      <button type="submit" class="cancel notext" onclick="$V(this.form.del, 1); onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Refuser">Refuser</button>
    </form>
  </td>
</tr>