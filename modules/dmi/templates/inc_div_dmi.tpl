<script type="text/javascript">
check_separator = function(element, next_element)
{
  if(element.value.charAt(element.value.length-1)==' ')
  {
    $(next_element).focus();
    element.value = element.value.substring(0,element.value.length-1);
    return false;
  }
  return true;
}
search_product = function(form)
{
  var url = new Url;
  url.setModuleAction("dmi", "httpreq_do_search_product");
  url.addParam("code", form.code.value);
  url.addParam("code_lot", form.code_lot.value);
  url.requestUpdate("product_description");
  return false;
}
search_product_order_item_reception = function()
{
  var form = getForm("dmi_delivery"); 
  var url = new Url;
  url.setModuleAction("dmi", "httpreq_do_search_product_order_item_reception");
  url.addParam("product_id", form.product_id.value);
  url.requestUpdate("list_product_order_item_reception");
  return false;
}
Main.add(function () {
  var formDmiDelivery = getForm("dmi_delivery");
  urlProduct = new Url();
  urlProduct.setModuleAction("dPstock", "httpreq_do_product_autocomplete");
  urlProduct.autoComplete(formDmiDelivery._view, formDmiDelivery._view.id+'_autocomplete', {
    minChars: 2,
    updateElement : function(element) {
      $V(formDmiDelivery.product_id, element.id.split('-')[1]);
      $V(formDmiDelivery._view, element.select(".view")[0].innerHTML);
      search_product_order_item_reception();
    }
  });
});
</script>
<form name="dmi_delivery" method="post" action="" onsubmit="return search_product(this)">

<input type="hidden" name="product_id" value="{{$product_id}}"/>
<input type="submit" style="display:none"/>

<table class="main">
  <tr>  
    <td>
      <table class="form">
        <tr>
          <th colspan="10" class="title">Recherche par code barre</th>
        </tr>
        <tr>
          <th>Code produit</th>
          <td><input type="text" name="code" onkeyup="return check_separator(this,this.form.code_lot)"/></td>
        </tr>
        <tr>
          <th>Code lot</th>
          <td><input type="text" name="code_lot" /></td>
        </tr>
        <tr>
          <th></th>
          <td id="product_description" colspan="10"></td>
        </tr>
      </table>
    </td>
    <td>
      <table class="form">
        <tr>
          <th colspan="10" class="title">Recherche par produit</th>
        </tr>
        <tr>
          <th>Produit</th>
          <td>
            <input type="text" name="_view" size="30" value="" />
            <div id="dmi_delivery__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
          </td>
        </tr>
        <tr>
          <th>Lot(s)</th>
          <td id="list_product_order_item_reception"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</form>
  <!-- <tr>
    <td>
      <table class="tbl">
       <tr>
         <th colspan="10">Recherche par code barre</th>
       </tr>
        <tr>
          <th>Code produit</th>
          <th>Code lot</th>
        </tr>
        <tr>
          <td><input type="text" name="code" onkeyup="return check_separator(this,this.form.code_lot)"/></td>
          <td><input type="text" name="code_lot" /></td>
        </tr>
        <tr>
         <th colspan="10">Recherche par produit</th>
        </tr>
        <tr>
          <th>Produit</th>
          <th>Lots</th>
        </tr>
        <tr>
          <td>
            <input type="text" name="_view" size="30" value="" />
            <div id="dmi_delivery__view_autocomplete" style="display: none; width: 300px;" class="autocomplete"></div>
          </td>
          <td id="list_product_order_item_reception"></td>
        </tr>
      </table>
    </td>
    <td id="product_description"></td>
  </tr>-->
