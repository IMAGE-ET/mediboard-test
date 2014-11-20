{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage Stock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=stock script=product_selector}}

<script type="text/javascript">
function refreshList(callback){
  var url = new Url("dPstock", "httpreq_vw_stocks_service_list");
  url.addFormData("filter-stocks");
  url.requestUpdate("list-stocks-service", callback);
  return false;
}

function changePage(page){
  $V(getForm("filter-stocks").start, page);
}

Main.add(function(){
  refreshList(
    refreshEditStock.curry('{{$stock->_id}}', '{{$stock->product_id}}', '{{$stock->object_id}}')
  );
});

function refreshEditStock(stock_id, product_id, service_id) {
  var url = new Url("dPstock", "httpreq_edit_stock_service");
  url.addParam("stock_service_id", stock_id);
  url.addNotNullParam("product_id", product_id);
  url.addNotNullParam("service_id", service_id);
  url.requestUpdate("edit-stock-service", function(){
    $("row-CProductStockService-"+stock_id).addUniqueClassName("selected");
  });
}
</script>

<table class="main">
  <tr>
    <td rowspan="3" class="halfPane">
      <form name="filter-stocks" action="?" method="get" onsubmit="return refreshList()">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        
        <select name="category_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="">&mdash; {{tr}}CProductCategory.all{{/tr}}</option>
          {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
          {{/foreach}}
        </select>
        
        <input type="hidden" name="object_class" value="CService" /> {{* XXX *}}
        <select name="object_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="">&mdash; {{tr}}CService.all{{/tr}}</option>
          {{foreach from=$list_services item=curr_service}}
          <option value="{{$curr_service->_id}}" {{if $service_id==$curr_service->_id}}selected="selected"{{/if}}>{{$curr_service->_view}}</option>
          {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" onchange="$V(this.form.start,0)" />
        
        <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit()">{{tr}}Clear{{/tr}}</button>
      </form>
  
      <div id="list-stocks-service"></div>
    </td>

    <td id="edit-stock-service"></td>
  </tr>
</table>