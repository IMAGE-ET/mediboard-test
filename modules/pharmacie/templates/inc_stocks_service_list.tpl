{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage pharmacie
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
// Autocomplete des medicaments
Main.add(function () {
  oFormDispensation = getForm('dispensation-urgence', true);

  var url = new Url("dPmedicament", "httpreq_do_medicament_autocomplete");
  url.addParam("produit_max", 40);
  url.autoComplete(oFormDispensation.produit, "produit_auto_complete", {
    minChars: 3,
    updateElement: updateFieldsMedicament,
    callback: 
      function(input, queryString){
        return queryString + "&inLivret=1&search_by_cis=0";
      }
  });
});

updateDispensationUrgence = function(formUrgence) {
  var formFilter = getForm("filter");
  $V(formUrgence.service_id, $V(formFilter.service_id));
  $V(formUrgence.patient_id, $V(formFilter.patient_id));
}
</script>

<table class="tbl">
  <tr>
    <th colspan="6" class="title">Dispensations - Produits présents dans les services</th>
  </tr>
  <tr>
    <th>{{tr}}CProductStockService-product_id{{/tr}}</th>
    {{if !$dPconfig.dPstock.CProductStockService.infinite_quantity}}
    <th>{{tr}}CProductStockService{{/tr}}</th>
    <th>{{tr}}CProductStockService-quantity{{/tr}}</th>
    {{/if}}
    <th>{{tr}}CProduct-_unit_title{{/tr}}</th>
    <th>{{tr}}CProductDelivery{{/tr}}</th>
    <th>Retour des services</th>
  </tr>
  {{foreach from=$list_stocks_service item=stock}}
    <tr>
      <td>
        <a class="tooltip-trigger" 
           onmouseover="ObjectTooltip.createEx(this, '{{$stock->_ref_product->_guid}}')"
           href="?m=dPstock&amp;tab=vw_idx_stock_service&amp;stock_service_id={{$stock->_id}}" title="{{tr}}CProductStockService-title-modify{{/tr}}">
        {{$stock->_ref_product}}
        </a>
      </td>
      {{if !$dPconfig.dPstock.CProductStockService.infinite_quantity}}
      <td>{{include file="../../dPstock/templates/inc_bargraph.tpl" stock=$stock}}</td>
      <td style="text-align: center;">{{mb_value object=$stock field=quantity}}</td>
      {{/if}}
      <td>{{mb_value object=$stock->_ref_product field=_unit_title}}</td>
      <td>
        {{assign var=id value=$stock->_id}}
        <form name="dispensation-{{$id}}" action="?" method="post" onsubmit="return (checkForm(this) && onSubmitFormAjax(this, {onComplete: refreshLists}))">
          <input type="hidden" name="m" value="dPstock" />
          <input type="hidden" name="dosql" value="do_delivery_aed" />
          {{mb_field object=$list_dispensations.$id field=service_id hidden=true}}
          {{mb_field object=$list_dispensations.$id field=patient_id hidden=true}}
          {{mb_field object=$list_dispensations.$id field=stock_id hidden=true}}
          <input type="hidden" name="date_dispensation" value="now" />
          {{mb_field object=$list_dispensations.$id field=quantity increment=1 form="dispensation-$id" size=3}}
          <button type="submit" class="tick">Dispenser</button>
        </form>
      </td>
      <td>
      {{if array_key_exists($id, $list_returns)}}
        {{foreach from=$list_returns.$id item=return}}
          {{assign var=id value=$return->_id}}
          <form name="return-{{$id}}" action="?" method="post" onsubmit="return (checkForm(this) && onSubmitFormAjax(this, {onComplete: refreshLists}))">
            <input type="hidden" name="m" value="dPstock" />
            <input type="hidden" name="dosql" value="do_delivery_trace_aed" />
            <input type="hidden" name="delivery_trace_id" value="{{$id}}" />
            {{mb_field object=$return field=code}}
            <input type="hidden" name="date_delivery" value="now" />
            {{mb_field object=$return field=quantity increment=1 form="return-$id" size=3}}
            <button type="submit" class="tick">Recevoir</button>
          </form><br />
        {{foreachelse}}
          Aucun retour de service
        {{/foreach}}
      {{else}}
        Aucun retour de service
      {{/if}}
      </td>
    </tr>
  {{foreachelse}}
    <tr>
      <td colspan="10">{{tr}}CProductStockService.none{{/tr}}</td>
    </tr>
  {{/foreach}}
  <tr>
    <th class="title" colspan="6">
			Autres produits
    </th>
  </tr>
  <tr>
    <td colspan="6" style="text-align: center;">
    <form name="dispensation-urgence" action="?" method="post" onsubmit="updateDispensationUrgence(this); $('produit_view').update(''); return (checkForm(this) && onSubmitFormAjax(this, {onComplete: refreshLists}))">
      <input type="hidden" name="m" value="dPstock" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      {{mb_field object=$delivrance field=service_id hidden=true}}
      {{mb_field object=$delivrance field=patient_id hidden=true}}
      <input type="hidden" name="date_dispensation" value="now" />
      <input type="hidden" name="_code" value="" class="notNull" />
      
      Produit: <input type="text" name="produit" value="" autocomplete="off" class="autocomplete" />
      <span id="produit_view"></span>
      <div style="display: none; text-align: left;" class="autocomplete" id="produit_auto_complete"></div>
      
      Quantité: {{mb_field object=$delivrance field=quantity size="4" increment=true form="dispensation-urgence" value="1"}}
      
      <button class="tick">Dispenser</button>
    </form>
    </td>
  </tr>
</table>