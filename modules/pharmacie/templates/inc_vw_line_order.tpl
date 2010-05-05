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
    {{if $curr_delivery->_ref_stock->_id}}
      <span onmouseover="ObjectTooltip.createEx(this, '{{$curr_delivery->_guid}}')">
        {{$curr_delivery->_ref_stock->_view}}
      </span>
    {{else}}
    
    {{* 
     <script type="text/javascript">
      var oFormDispensation = getForm('dispensation-stock-{{$curr_delivery->_id}}');
    
      var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
      url.addParam("produit_max", 40);
      url.autoComplete(oFormDispensation.produit, $(oFormDispensation.produit).next(), {
        minChars: 3,
        select: "libelle",
        afterUpdateElement: function(input, selected) {
          $V(input.form._code, selected.down(".code-cip").innerHTML);
        },
        callback: function(input, queryString){
          return queryString + "&inLivret=1&search_by_cis=0";
        }
      });
     </script>
    *}}
    
    <form name="dispensation-stock-{{$curr_delivery->_id}}" onsubmit="return false" action="?" method="post">
      <input type="hidden" name="m" value="dPstock" /> 
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="delivery_id" value="{{$curr_delivery->_id}}" />
      
      {{* 
      <input type="hidden" name="_code" value="" />
      Produit: 
      <input type="text" name="produit" value="" autocomplete="off" class="autocomplete" />
      <div style="display: none; text-align: left;" class="autocomplete"></div> 
      *}}
      
      {{mb_field object=$curr_delivery->_ref_stock field=product_id form=dispensation-stock-$id autocomplete="true,1,50,false,true"}}
      {{mb_field object=$curr_delivery field=quantity increment=1 form=dispensation-stock-$id size=3}}

      <button type="submit" class="tick notext" onclick="onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Dispenser">Dispenser</button>
      <button type="submit" class="cancel notext" onclick="$V(this.form.del, 1); $V(this.form.stock_id, ''); this.form.product_id.className = ''; onSubmitFormAjax(this.form, {onComplete: refreshOrders})" title="Refuser">Refuser</button>
    </form>
    {{/if}}
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
    {{if $curr_delivery->_ref_stock->_id}}
      {{$curr_delivery->_ref_stock->_ref_product->_unit_title}}
    {{/if}}
  </td>
  <td>
    {{if $curr_delivery->_ref_stock->_id}}
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
    {{else}}
    
    {{/if}}
  </td>
</tr>