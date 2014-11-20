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
  var url = new Url("dPstock", "httpreq_vw_stocks_group_list");
  url.addFormData("filter-stocks");
  url.requestUpdate("list-stocks-group", callback);
  return false;
}

function changePage(page){
  $V(getForm("filter-stocks").start, page);
}

function changeLetter(letter) {
  var form = getForm("filter-stocks");
  $V(form.start, 0, false);
  $V(form.letter, letter);
}

Main.add(function(){
  refreshList(
    refreshEditStock.curry('{{$stock->_id}}', '{{$stock->product_id}}')
  );
});

function refreshEditStock(stock_id, product_id) {
  var url = new Url("dPstock", "httpreq_edit_stock_group");
  url.addParam("stock_id", stock_id);
  url.addNotNullParam("product_id", product_id);
  url.requestUpdate("edit-stock-group", function(){
    $("row-CProductStockGroup-"+stock_id).addUniqueClassName("selected");
  });
}
</script>

<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
      <form name="filter-stocks" action="?" method="get" onsubmit="return refreshList()">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="start" value="0" onchange="this.form.onsubmit()" />
        <input type="hidden" name="letter" value="{{$letter}}" onchange="this.form.onsubmit()" />
        
        <select name="category_id" onchange="$V(this.form.start,0);this.form.onsubmit()">
          <option value="">&mdash; {{tr}}CProductCategory.all{{/tr}}</option>
          {{foreach from=$list_categories item=curr_category}}
          <option value="{{$curr_category->category_id}}" {{if $category_id==$curr_category->_id}}selected="selected"{{/if}}>{{$curr_category->name}}</option>
          {{/foreach}}
        </select>
        
        <input type="text" name="keywords" value="" onchange="$V(this.form.start,0)" />
        
        <button type="submit" class="search notext">{{tr}}Filter{{/tr}}</button>
        <button type="button" class="cancel notext" onclick="$(this.form).clear(false); this.form.onsubmit()">{{tr}}Clear{{/tr}}</button>
        <br />
    
        <input type="checkbox" name="only_ordered_stocks" onchange="$V(this.form.start,0);this.form.onsubmit()" />
        <label for="only_ordered_stocks">Seulement les stocks en cours de réapprovisionnement</label>
        
        {{mb_include module=system template=inc_pagination_alpha current=$letter change_page=changeLetter}}
      </form>
  
      <div id="list-stocks-group"></div>
    </td>

    <td class="halfPane" id="edit-stock-group"></td>
  </tr>
</table>