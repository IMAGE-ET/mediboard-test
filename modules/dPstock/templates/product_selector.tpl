<script type="text/javascript">
Main.add(function () {
  refreshCategoriesList(null, '{{$selected_category}}');
  
  {{if $selected_category}}
    refreshProductsList('{{$selected_category}}', null, '{{$selected_product}}');
    refreshProductInfo({{$selected_product}});
  {{else}}
    refreshProductsList(-1, null{{if $selected_product}}, {{$selected_product}}{{/if}});
  {{/if}}
});

function setClose(oForm) {
  if (oForm) {
    if ($V(oForm.product) > 0) {
      var name = oForm.product.options[oForm.product.selectedIndex].text;
      var oSelector = window.opener.ProductSelector;
      oSelector.set($V(oForm.product), name, $V(oForm._unit_quantity), $V(oForm._unit_title), $V(oForm.packaging));
    }
  }
  window.close();
}

/// category_id == -1   ->   we get an empty list
/// category_id ==  0   ->   we get every product
function refreshProductsList(category_id, keywords, selected_product) {
  if (!keywords || keywords.length >= 2) {
    url = new Url;
    url.setModuleAction("dPstock","httpreq_product_selector_products_list");
    url.addParam("category_id", category_id);
    url.addParam("keywords", keywords);
    url.addParam("selected_product", selected_product);
    url.requestUpdate("products", { waitingText: null } );
  }
}

function refreshCategoriesList(keywords, selected_category) {
  if (!keywords || keywords.length >= 2) {
    url = new Url;
    url.setModuleAction("dPstock","httpreq_product_selector_categories_list");
    url.addParam("keywords", keywords);
    url.addParam("selected_category", selected_category);
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
<table class="main">
  <tr>
    <th class="title" style="width: 1%;">{{tr}}CProductCategory{{/tr}}</th>
    <th class="title" style="width: 1%;">{{tr}}CProduct{{/tr}}</th>
    <th class="title">{{tr}}Information{{/tr}}</th>
  </tr>
  <tr>
    <td>
      <input type="text" name="search_category" size="20" value="" onkeyup="refreshCategoriesList(this.value);" />
      <button class="cancel notext" id="clear_category" onclick="refreshCategoriesList(); this.form.search_category.value='';">{{tr}}Reset{{/tr}}</button>
    </td>
    <td>
      <input type="text" name="search_product" size="20" value="" onkeyup="refreshProductsList(null, this.value);" />
      <button class="cancel notext" id="clear_product" onclick="refreshProductsList(); this.form.search_product.value='';">{{tr}}Reset{{/tr}}</button>
    </td>
    <td id="product_info" style="vertical-align: top;" rowspan="2"></td>
  </tr>
  <tr>
    <td id="categories" rowspan="2"></td>
    <td id="products"   rowspan="2"></td>
  </tr>
  <tr style="height:1%;">
    <td>
      <button class="tick" id="setclose_button" onclick="setClose(this.form);">{{tr}}Select{{/tr}}</button>
      <button class="cancel" id="close_button" onclick="setClose();">{{tr}}Cancel{{/tr}}</button>
    </td>
  </tr>
</table>
</form>