<script type="text/javascript">
function pageMain() {
  refreshCategoriesList();
  refreshProductsList(-1);
  
  new Ajax.Autocompleter(
    'search_product',
    'product_id',
    '?m=dPstock&ajax=1&suppressHeaders=1&a=httpreq_product_selector_list_products&search_field=search_product', {
      method: 'get',
      minChars: 3,
      frequency: 0.15
    }
  );
}

function setClose(product_id) {
  var oSelector = window.opener.ProductSelector;
  oSelector.set(product_id);
  window.close();
}

function refreshProductsList(category_id, search_string) {
  if (!search_string || search_string.length > 2) {
    url = new Url;
    url.setModuleAction("dPstock","httpreq_product_selector_list_products");
    url.addParam("category_id", category_id);
    url.addParam("search_string", search_string);
    url.requestUpdate("product_id", { waitingText: null } );
  }
}

function refreshCategoriesList(search_string) {
  if (!search_string || search_string.length > 2) {
    url = new Url;
    url.setModuleAction("dPstock","httpreq_product_selector_list_categories");
    url.addParam("search_string", search_string);
    url.requestUpdate("category_id", { waitingText: null } );
  }
}

function refreshProductInfo(product_id) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_product_selector_product_info");
  url.addParam("product_id", product_id);
  url.requestUpdate("product_info", { waitingText: null } );
}

</script>

<form name="product_selector" action="" method="get" onsubmit="false">
<table>
  <tr>
    <th style="width: 0;">
      Catégorie<br />
      <input type="text" name="search_category" size="20" value="" onchange="refreshCategoriesList(this.value);" />
      <button class="cancel notext" id="clear_category"></button>
    </th>
    <th style="width: 0;">
      Produit<br />
      <input type="text" name="search_product" size="20" value="" onchange="refreshProductsList(null, this.value);" />
      <button class="cancel notext" id="clear_product"></button>
    </th>
    <th style="vertical-align: bottom;">Informations sur le produit :</th>
  </tr>
  <tr>
    <td style="width: 0;">
      <select name="category_id" id="category_id" onchange="refreshProductsList(this.value); this.form.search_category.value=''; this.form.search_product.value='';" size="20" style="width: 100%;"></select>
    </td>
    <td>
      <select name="product_id" id="product_id" size="20" style="width: 100%;" onchange="refreshProductInfo(this.value);"></select>
    </td>
    <td id="product_info" style="vertical-align: top;"></td>
  </tr>
</table>
</form>