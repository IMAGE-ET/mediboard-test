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
  <td>
    <a href="javascript:;" onmouseover="ObjectTooltip.createEx(this, '{{$stock->_ref_product->_guid}}');" class="tooltip-trigger">
    {{$stock}}
    </a>
  </td>

  <td style="text-align: right;">
   {{if $stock->_ref_product->item_title}}
   {{$stock->_ref_product->item_title}} x {{$stock->_ref_product->unit_quantity}}
   {{/if}}
   {{$stock->_ref_product->unit_title}}
  </td>

  <!-- Formulaire de dispensation -->
  <td style="text-align: left;">
    <script type="text/javascript">prepareForm('form-dispensation-{{$stock->_id}}');</script>
    <form name="form-dispensation-{{$stock->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: refreshLists})">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="date_dispensation" value="now" />
      <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="service_id" value="{{$service->_id}}" />
      <input type="hidden" name="order" value="1" />

      {{*if $delivrance->quantity == 0}}
        {{assign var=style value="opacity: 0.5; -moz-opacity: 0.5;"}}
      {{else*}}
        {{assign var=style value=""}}
      {{*/if*}}
      
      <button type="submit" class="tick notext" title="Dispenser" style="{{$style}}">Dispenser</button>
      
      {{assign var=qty value=$stock->_ref_product->_unit_quantity-0}}
      {{assign var=stock_id value=$stock->_id}}
      {{if $stock->_ref_product->packaging && $qty}}
        {{mb_field object=$stock field=quantity form="form-dispensation-$stock_id" increment=1 size=3 min=0 value=$qty style=$style 
         onchange="this.form._quantity_package.value = this.value/$qty"}}
       
       (soit <input type="text" name="_quantity_package" value="{{if $qty}}1{{else}}0{{/if}}" size="3" 
              onchange="$V(this.form.quantity, Math.round($V(this)*{{$qty}}))" style="{{$style}}" />
       {{$stock->_ref_product->packaging}})
       <script type="text/javascript">
         getForm("form-dispensation-{{$stock->_id}}")._quantity_package.addSpinner({min:0});
       </script>
      {{else}}
        {{mb_field object=$stock field=quantity form="form-dispensation-$stock_id" increment=1 size=3 min=0 style=$style}}
      {{/if}}
    </form>
  </td>
  
  <!-- Affichage des dispensations deja effectuées -->
  <td style="text-align: left" class="text">  
  {{foreach from=$stock->_ref_deliveries item=dispensation}}
    {{if $dispensation->order == 1}}
     <form name="form-dispensation-del-{{$dispensation->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete:refreshLists})">
       <input type="hidden" name="m" value="dPstock" />
       <input type="hidden" name="dosql" value="do_delivery_aed" />
       <input type="hidden" name="del" value="1" />
       <input type="hidden" name="delivery_id" value="{{$dispensation->_id}}" />
       <button type="submit" class="cancel notext" title="{{tr}}Cancel{{/tr}}">{{tr}}Cancel{{/tr}}</button>
     </form>
     {{else}}
       <img src="images/icons/tick.png" alt="Dispensé" title="Dispensé" />
     {{/if}}
     {{$dispensation->quantity}} le {{mb_value object=$dispensation field=date_dispensation}}
     <br />
  {{/foreach}}
  </td>
</tr>