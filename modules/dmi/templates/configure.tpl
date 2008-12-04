<h2>Configuration générale</h2>

<script type="text/javascript">

function startSyncProducts(category_id){
  if (category_id) {
    var url = new Url;
    url.setModuleAction("dmi", "httpreq_do_sync_products");
    url.addParam("category_id", category_id);
    url.requestUpdate("do_sync_products");
  }
}

</script>

<table class="tbl">
  <tr>
    <td>
      <form name="sync-products" action="" onsubmit="return false">
        {{assign var="class" value="CDMI"}}
        {{assign var="var" value="product_category_id"}}
        <select name="{{$m}}[{{$class}}][{{$var}}]" class="notNull">
          <option value="">{{tr}}CProductCategory.select{{/tr}}</option>
          {{foreach from=$categories_list item=category}}
            <option value="{{$category->_id}}" {{if $category->_id==$dPconfig.$m.$class.$var}}selected="selected"{{/if}}>{{$category->name}}</option>
          {{/foreach}}
        </select>
        <button class="tick" onclick="if (!checkForm(this.form)) return false; startSyncProducts($V(this.form['{{$m}}[{{$class}}][{{$var}}]']));" >Synchroniser les produits du stock</button>
      </form>
    </td>
    <td id="do_sync_products"></td>
  </tr>
</table>