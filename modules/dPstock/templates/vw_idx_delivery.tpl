{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPstock script=filter}}

<script type="text/javascript">
Main.add(function () {
  filterFields = ["category_id", "keywords"];
  stocksFilter = new Filter("filter-stocks", "{{$m}}", "httpreq_vw_delivery_stocks_list", "delivery-list-stocks", filterFields);
  stocksFilter.submit();
  refreshDeliveriesList();
});

function refreshDeliveriesList() {
  var url = new Url("dPstock","httpreq_vw_deliveries_list");
  url.requestUpdate("deliveries");
}

function refreshStock(stock_id) {
  var url = new Url("dPstock","httpreq_vw_delivery_stock_item");
  url.addParam("stock_id", stock_id);
  url.requestUpdate("delivery-"+stock_id);
}

function deliver(oForm, sign) {
  if (sign == undefined) sign = 1;
  oForm.service_id.value = $V($('service_id'));
  oForm.quantity.value = $V(oForm.quantity) * sign;
  var stock_id = $V(oForm.stock_id);
  
  submitFormAjax(oForm, 'systemMsg', {
    onComplete: function() {
      refreshValue('CProductStockGroup-'+stock_id, 'bargraph', function(v){$('stock-'+stock_id+'-bargraph').update(v)});
      refreshDeliveriesList();
    }
  });
}
</script>

<table class="main">
  <tr>
    <td class="halfPane">

      <form name="filter-stocks" action="?" method="post" onsubmit="return stocksFilter.submit('keywords');">
        <input type="hidden" name="m" value="{{$m}}" />
        
        <select name="category_id" onchange="stocksFilter.submit();">
          <option value="0" >&mdash; {{tr}}CProductCategory.all{{/tr}} &mdash;</option>
        {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
        {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" />
        <button type="button" class="search" onclick="stocksFilter.submit('keywords');">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="stocksFilter.empty();">{{tr}}Reset{{/tr}}</button><br />
      </form>

      <div id="delivery-list-stocks"></div>
      
      <label for="service_id">{{tr}}CProductDelivery-service_id{{/tr}} </label>
      <select name="service_id" id="service_id">
        {{foreach from=$list_services item=curr_service}}
        <option value="{{$curr_service->_id}}">{{$curr_service->_view}}</option>
        {{/foreach}}
      </select>

    </td>
    <td class="halfPane" id="deliveries"></td>
  </tr>
</table>

