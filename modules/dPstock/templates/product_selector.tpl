<script type="text/javascript">
function pageMain() {
  refreshCategoriesList();
  refreshProductsList(-1);
}

function setClose(oField) {
  if (oField) {
    if (oField.value > 0) {
      var name = oField.options[oField.selectedIndex].text;
      var oSelector = window.opener.ProductSelector;
      oSelector.set(oField.value, name);
    }
  }
  window.close();
}

function refreshProductsList(category_id, search_string) {
  if (!search_string || search_string.length >= 2) {
    url = new Url;
    url.setModuleAction("dPstock","httpreq_product_selector_list_products");
    url.addParam("category_id", category_id);
    url.addParam("search_string", search_string);
    url.requestUpdate("products", { waitingText: null } );
  }
}

function refreshCategoriesList(search_string) {
  if (!search_string || search_string.length >= 2) {
    url = new Url;
    url.setModuleAction("dPstock","httpreq_product_selector_list_categories");
    url.addParam("search_string", search_string);
    url.requestUpdate("categories", { waitingText: null } );
  }
}

function refreshProductInfo(product_id) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_product_selector_product_info");
  url.addParam("product_id", product_id);
  url.requestUpdate("product_info", { waitingText: null } );
}

</script>

<form name="form_product_selector" action="" method="get" onsubmit="return false">
<table>
  <tr>
    <th style="width: 0;">
      Catégorie<br />
      <input type="text" name="search_category" size="20" value="" onkeydown="refreshCategoriesList(this.value);" />
      <button class="cancel notext" id="clear_category" onclick="refreshCategoriesList(); this.form.search_category.value='';">Effacer</button>
    </th>
    <th style="width: 0;">
      Produit<br />
      <input type="text" name="search_product" size="20" value="" onkeydown="refreshProductsList(null, this.value);" />
      <button class="cancel notext" id="clear_product" onclick="refreshProductsList(); this.form.search_product.value='';">Effacer</button>
    </th>
    <th style="vertical-align: bottom;">Informations sur le produit :</th>
  </tr>
  <tr>
    <td id="categories" rowspan="2" style="width: 0;"></td>
    <td id="products"   rowspan="2" style="width: 0;"></td>
    <td id="product_info" style="vertical-align: top;"></td>
  </tr>
  <tr height="1">
    <td>
      <button class="tick" id="setclose_button" onclick="setClose(this.form.product);">Sélectionner</button>
      <button class="cancel" id="close_button" onclick="setClose();">Annuler</button>
    </td>
  </tr>
</table>
</form>