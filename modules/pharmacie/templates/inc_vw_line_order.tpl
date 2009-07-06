{{* $Id: inc_vw_line_delivrance.tpl 6146 2009-04-21 14:40:08Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td>
    {{if $curr_delivery->patient_id}}
      {{$curr_delivery->_ref_patient->_view}}
    {{else}}
      {{$curr_delivery->_ref_service->_view}}
    {{/if}}
  </td>
  <td>
    <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">{{$curr_delivery->_ref_stock->_view}}</div>
    <div class="tooltip-trigger" 
         onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_delivery->_id}}')">
      {{$curr_delivery->_ref_stock->_view}}
    </div>
  </td>
  {{if !$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
  <td>
    <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_id={{$curr_delivery->_ref_stock->_id}}" title="{{tr}}CProductStockGroup-title-modify{{/tr}}">
      {{mb_value object=$curr_delivery->_ref_stock field=quantity}}
    </a>
  </td>
  {{/if}}
  <td style="text-align: center;">{{mb_value object=$curr_delivery field=quantity}}</td>
  <td style="text-align: center;">
    {{assign var=id value=$curr_delivery->_id}}
    {{assign var=stock value=$curr_delivery->_ref_stock}}
    <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_service_id={{$stock->_id}}" title="{{tr}}CProductStockService-title-modify{{/tr}}">
      {{$stock->quantity}}
    </a>
  </td>
  <td style="text-align: center;">
    {{$curr_delivery->_ref_stock->_ref_product->_unit_title}}
  </td>
  <td>
    <script type="text/javascript">
      prepareForm("dispensation-validate-{{$curr_delivery->_id}}");
    </script>
    <form name="dispensation-validate-{{$curr_delivery->_id}}" onsubmit="return false" action="?" method="post" {{if $curr_delivery->isDelivered()}}style="opacity: 0.4;"{{/if}}>
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      <input type="hidden" name="date_dispensation" value="now" />
      <input type="hidden" name="order" value="0" />
      {{mb_field object=$curr_delivery field=quantity increment=1 form=dispensation-validate-$id size=3 value=$curr_delivery->quantity-$curr_delivery->countDelivered()}}
      <button type="submit" class="tick" onclick="onSubmitFormAjax(this.form, {onComplete: refreshOrders})">Dispenser</button>
      <button type="submit" class="cancel" onclick="$V(this.form.del, 1); onSubmitFormAjax(this.form, {onComplete: refreshOrders})">Refuser</button>
    </form>
  </td>
</tr>