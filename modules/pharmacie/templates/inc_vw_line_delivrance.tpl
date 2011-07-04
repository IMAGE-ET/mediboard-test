{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=id value=$curr_delivery->_id}}
{{assign var=product value=$curr_delivery->_ref_stock->_ref_product}}

<td {{if $product->_in_order}}class="ok"{{/if}}>
  {{mb_include module=dPstock template=inc_product_in_order product=$product}}
</td>

{{if $curr_delivery->patient_id}}
  <td>{{$curr_delivery->_ref_patient->_view}}</td>
{{/if}}
<td>
  {{if @$line_refresh}}
  <script type="text/javascript">
    if (!$V(getForm("filter").display_delivered) && ({{$curr_delivery->_delivered|ternary:1:0}} || '{{$curr_delivery->date_delivery}}')) {
      $("CProductDelivery-{{$curr_delivery->_id}}").hide();
      /*Main.add(function(){
        //$("CProductDelivery-{{$curr_delivery->_id}}").remove();
      });*/
    }
  </script>
  {{/if}}
  
  <div id="tooltip-content-{{$curr_delivery->_id}}" style="display: none;">{{$curr_delivery->_ref_stock->_view}}</div>
  <span onmouseover="ObjectTooltip.createDOM(this, 'tooltip-content-{{$curr_delivery->_id}}')">
    {{$curr_delivery->_ref_stock->_view}}
  </span>
  {{if $curr_delivery->comments}}
   - [{{$curr_delivery->comments}}]
  {{/if}}
</td>
<td style="text-align: center;">{{mb_ditto name="date-$service_id" value=$curr_delivery->date_dispensation|date_format:$conf.date}}</td>
<td style="text-align: center;">{{mb_ditto name="time-$service_id" value=$curr_delivery->date_dispensation|date_format:$conf.time}}</td>

{{if !$conf.dPstock.CProductStockGroup.infinite_quantity}}
<td style="text-align: center;">
  {{mb_value object=$curr_delivery->_ref_stock field=quantity}}
</td>
{{/if}}

<td>
  {{mb_value object=$curr_delivery->_ref_stock->_ref_location field=name}}
</td>

{{if !$conf.dPstock.CProductStockService.infinite_quantity}}
<td style="text-align: center;">
  {{assign var=stock value=$stocks_service.$id}}
  {{$stock->quantity}}
</td>
{{/if}}

<td>
  {{assign var=remaining value=$curr_delivery->quantity-$curr_delivery->countDelivered()}}
  
  {{if $remaining < 1}}
    <button type="button" class="down notext" onclick="ObjectTooltip.createDOM(this, $(this).next('table'), {duration:0})" style="margin: -1px;"></button> 
    {{$remaining}} restants
  {{/if}}
  
  <table class="layout" {{if $remaining < 1}}style="display: none"{{/if}}>
  {{foreach from=$curr_delivery->_ref_delivery_traces item=trace}}
    <tr>
      <td class="button narrow">
        {{if !$trace->date_reception}}
          <form name="delivery-trace-{{$trace->_id}}-cancel" action="?" method="post" onsubmit="return deliverLine(this)">
            <input type="hidden" name="m" value="dPstock" /> 
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
            <input type="hidden" name="delivery_trace_id" value="{{$trace->_id}}" />
            <input type="hidden" name="_delivery_id" value="{{$curr_delivery->_id}}" /> <!-- used by refreshDeliveryLine -->
            <input type="hidden" name="_undeliver" value="1" />
            <button type="submit" class="cancel notext oneclick" style="margin: -1px;">{{tr}}Cancel{{/tr}}</button>
          </form>
        {{else}}
          <img src="images/icons/tick.png" title="Délivré" />
        {{/if}}
      </td>
      <td class="narrow">
        {{$trace->quantity}} éléments
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
  
    <tr>
      <td colspan="10" title="Quantité d'origine: {{mb_value object=$curr_delivery field=quantity}}" style="padding: 0;">
        <form name="delivery-trace-{{$curr_delivery->_id}}-new" action="?" method="post" class="deliver"
              onsubmit="return deliverLine(this)" {{if $curr_delivery->_delivered}}style="display: none;"{{/if}}>
          <input type="hidden" name="m" value="dPstock" /> 
          <input type="hidden" name="del" value="0" />
          <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
          <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
          <input type="hidden" name="date_delivery" value="now" />
          {{mb_field object=$curr_delivery field=quantity increment=1 form=delivery-trace-$id-new size=2 value=$remaining}}
          <input type="text" name="code" value="" size="8" />
          <button type="submit" class="tick notext" style="margin: -1px;">Délivrer</button>
        </form>
      </td>
    </tr>
  </table>
</td>
<td class="text">
  {{$curr_delivery->_ref_stock->_ref_product->_unit_title}}
</td>
<td>
  <form name="delivery-force-{{$curr_delivery->_id}}-receive" action="?" method="post" 
        onsubmit="return onSubmitFormAjax(this, {onComplete: refreshDeliveryLine.curry($V(this.delivery_id))})">
    <input type="hidden" name="m" value="dPstock" /> 
    <input type="hidden" name="del" value="0" />
    <input type="hidden" name="dosql" value="do_delivery_aed" />
    {{mb_key object=$curr_delivery}}
    <input type="hidden" name="date_delivery" value="{{if !$curr_delivery->date_delivery}}now{{/if}}" />
    <button type="submit" class="{{$curr_delivery->date_delivery|ternary:'tick':'cancel'}} notext" style="margin: -1px;" title="Marquer comme{{$curr_delivery->date_delivery|ternary:' non':''}} reçu"></button>
  </form>
</td>