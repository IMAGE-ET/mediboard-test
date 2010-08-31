{{* $Id: inc_dispensation_line.tpl 6441 2009-06-19 09:47:12Z alexis_granger $ *}}

{{*
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6441 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{assign var=infinite value=$dPconfig.dPstock.CProductStockGroup.infinite_quantity}}
{{assign var=infinite_service value=$dPconfig.dPstock.CProductStockService.infinite_quantity}}

<tr>
<!-- Stock Pharmacie -->
  <!-- Affichage des stocks du service -->
  <td class="text">
    {{$stock->_ref_product->code}}
  </td>
  <td class="text">
    <strong onmouseover="ObjectTooltip.createEx(this, '{{$stock->_ref_product->_guid}}');">
      {{$stock}}
    </strong>
  </td>

  <td style="text-align: right;" class="text">
    {{if $stock->_ref_product->item_title}}
      {{$stock->_ref_product->item_title}}
    {{else}}
      <em style="color: #aaa">Sans unité de délivrance</em>
    {{/if}}
  </td>

  <!-- Formulaire de dispensation -->
  <td style="text-align: left;">
    <form name="form-dispensation-{{$stock->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: {{if isset($stock->_endowment_item_id|smarty:nodefaults)}}refreshOrders.curry({{$stock->_endowment_item_id}}, '{{$stock->_id}}'){{else}}refreshLists{{/if}}})">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="date_dispensation" value="now" />
      {{if $endowment_id && $dPconfig.dPstock.CProductDelivery.auto_dispensation}}
        <input type="hidden" name="_auto_deliver" value="1" />
      {{/if}}
      <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="service_id" value="{{$service->_id}}" />
      <input type="hidden" name="order" value="1" />

      {{*if $delivrance->quantity == 0}}
        {{assign var=style value="opacity: 0.5;"}}
      {{else*}}
        {{assign var=style value=""}}
      {{*/if*}}
      
      {{assign var=qty value=$stock->_ref_product->_unit_quantity-0}}
      
      {{assign var=stock_id value=$stock->_id}}
      {{if $stock->_ref_product->packaging && $qty && !$endowment_id}}
        {{mb_field object=$stock field=quantity form="form-dispensation-$stock_id" increment=1 size=3 min=1 value=$qty style=$style 
         onchange="this.form._quantity_package.value = this.value/$qty" class="num notNull min|1"}}
      {{else}}
        {{mb_field object=$stock field=quantity form="form-dispensation-$stock_id" prop="num notNull min|1" increment=1 size=3 min=1 style=$style}}
      {{/if}}
      <button type="button" class="down notext" title="{{tr}}CProductDelivery-comments-desc{{/tr}}" onclick="$(this).next('input[name=comments]').show().focus()"></button>
      <button type="submit" class="tick notext singleclick" title="Dispenser" style="{{$style}}">Dispenser</button>
      
      {{if $stock->_ref_product->packaging && $qty && !$endowment_id}}
        (soit <input type="text" name="_quantity_package" value="{{if $qty}}1{{else}}0{{/if}}" size="2" 
                     onchange="$V(this.form.quantity, Math.round($V(this)*{{$qty}}))" style="{{$style}}" />
        {{$stock->_ref_product->packaging}})
        <script type="text/javascript">
          getForm("form-dispensation-{{$stock->_id}}")._quantity_package.addSpinner({min:1});
        </script>
      {{/if}}
      <br />
      <input type="text" name="comments" style="display: none; width: 100%;" />
    </form>
  </td>
  
  <!-- Affichage des dispensations deja effectuées -->
  <td style="text-align: left" class="text" title="Dans {{$stock->_ref_deliveries|@count}} commandes">
    {{if $stock->_total_quantity}}
      <button class="down notext" type="button" onclick="$(this).next('table').toggle()"></button>
      {{$stock->_total_quantity}}
    
      <table class="layout" style="display: none;">
      {{foreach from=$stock->_ref_deliveries item=dispensation}}
        <tr>
          <td>
            {{if !$dispensation->countBackRefs('delivery_traces')}}
              <form name="form-dispensation-del-{{$dispensation->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete:{{if isset($stock->_endowment_item_id|smarty:nodefaults)}}refreshOrders.curry({{$stock->_endowment_item_id}}, {{$stock->_id}}){{else}}refreshLists{{/if}}})">
                <input type="hidden" name="m" value="dPstock" />
                <input type="hidden" name="dosql" value="do_delivery_aed" />
                <input type="hidden" name="del" value="1" />
                <input type="hidden" name="delivery_id" value="{{$dispensation->_id}}" />
                <button type="submit" class="cancel notext" title="{{tr}}Cancel{{/tr}}">{{tr}}Cancel{{/tr}}</button>
              </form>
            {{else}}
              <img src="images/icons/tick.png" title="Délivré" />
            {{/if}}
          </td>
          <td>
            <span onmouseover="ObjectTooltip.createEx(this, '{{$dispensation->_guid}}');">
              {{$dispensation->quantity}} le {{mb_value object=$dispensation field=date_dispensation}}
            </span>
          </td>
        </tr>
      {{/foreach}}
      </table>
    {{/if}}
  </td>
  
  {{if !$infinite_service}}
    {{if isset($stock->_ref_stock_service|smarty:nodefaults) && $stock->_ref_stock_service->_id}}
      <td>
        {{mb_label object=$stock->_ref_stock_service field=quantity}}:
        <strong>{{mb_value object=$stock->_ref_stock_service field=quantity}}</strong>
      </td>
      <td>
        {{mb_label object=$stock->_ref_stock_service field=order_threshold_optimum}}:
        <strong>{{mb_value object=$stock->_ref_stock_service field=order_threshold_optimum}}</strong>
      </td>
      <td>
        {{include file="../../dPstock/templates/inc_bargraph.tpl" stock=$stock->_ref_stock_service}}
      </td>
    {{else}}
      <td colspan="3">
        {{tr}}CProductStockService.none{{/tr}}
      </td>
    {{/if}}
  {{/if}}
</tr>