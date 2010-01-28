{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
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
    <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_delivery->_id}}')">
      {{$curr_delivery->_ref_stock->_view}}
    </span>
  </td>
  <td>{{mb_value object=$curr_delivery field=date_dispensation}}</td>
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
    {{assign var=stock value=$stocks_service.$id}}
    <a href="?m=dPstock&amp;tab=vw_idx_stock_group&amp;stock_service_id={{$stock->_id}}" title="{{tr}}CProductStockService-title-modify{{/tr}}">
      {{$stock->quantity}}
    </a>
  </td>
  <td style="text-align: center;">
    {{$curr_delivery->_ref_stock->_ref_product->_unit_title}}
  </td>
  <td>
    <table class="layout">
    {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
      <tr>
        <td class="button" style="width: 0.1%;">
          {{if !$trace->date_reception}}
            <form name="delivery-trace-{{$trace->_id}}-cancel" action="?" method="post" onsubmit="return deliverLine(this)">
              <input type="hidden" name="m" value="dPstock" /> 
              <input type="hidden" name="del" value="0" />
              <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
              <input type="hidden" name="delivery_trace_id" value="{{$trace->_id}}" />
              <input type="hidden" name="_undeliver" value="1" />
              <button type="submit" class="cancel notext">{{tr}}Cancel{{/tr}}</button>
            </form>
          {{else}}
            <img src="images/icons/tick.png" title="Délivré" />
          {{/if}}
        </td>
        <td style="width: 0.1%;">
          <strong>{{$trace->quantity}} éléments</strong>
        </td>
        <td>
          {{$trace->date_delivery|@date_format:"%d/%m/%Y"}}
        </td>
        <td>
          {{if $trace->code}}
            [{{$trace->code}}] 
          {{/if}}
        </td>
      </tr>
    {{/foreach}}
    
      {{assign var=_delivered value=$curr_delivery->isDelivered()}}
      <tr>
        <td>
          <button type="button" class="add notext" onclick="$(this).up().next().down('form').toggle()" {{if !$_delivered}}style="visibility: hidden;"{{/if}}></button>
        </td>
        <td colspan="10">
          <form name="delivery-trace-{{$curr_delivery->_id}}-new" action="?" method="post" 
                onsubmit="return deliverLine(this)" {{if $_delivered}}style="display: none;"{{/if}}>
            <input type="hidden" name="m" value="dPstock" /> 
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
            <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
            <input type="hidden" name="date_delivery" value="now" />
            {{mb_field object=$curr_delivery field=quantity increment=1 form=delivery-trace-$id-new size=3 value=$curr_delivery->quantity-$curr_delivery->countDelivered()}}
            <input type="text" name="code" value="" />
            <button type="submit" class="tick notext" title="Délivrer">Délivrer</button>
          </form>
        </td>
      </tr>
    </table>
  </td>
</tr>